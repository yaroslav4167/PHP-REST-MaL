<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
  http_response_code(405);
  exit;
}

if (!isJson(@$_POST['data'])) {
  $result['error'] = 'Input data is not a valid JSON!';
  returnResult($result);
}

$body_data = $_POST['data'];
$arr_data = json_decode($body_data, true);

if (!arrayNeedleKeyCheck(['id'], $arr_data)) {
  $result['error'] = 'One of required keys ("id") not found!';
  returnResult($result);
}

$update_fields = [
  // [field_sql_name, json_key, type]
  ['name', $db_config['column_magazine_name'], 'needle'],
  ['date', $db_config['column_magazine_date'], 'default'],
  ['description', $db_config['column_magazine_description'], 'default'],
  ['image', $db_config['column_magazine_image'], 'image'],
  ['authors', $db_config['column_magazine_authors'], 'authors']
];

$update_fields_sql = [];
$update_fields_sql_data = [];

foreach ($update_fields as $update_field) {
  switch ($update_field[2]) {
    case 'needle':
      if(array_key_exists($update_field[0], $arr_data)){
        if(empty($arr_data[$update_field[0]])) {
          $result['error'] = "Field ({$update_field[0]}) not be empty!";
          returnResult($result);
        }
        $update_fields_sql[] = "`{$update_field[1]}`= :{$update_field[0]}";
        $update_fields_sql_data[] = [":{$update_field[0]}", $arr_data[$update_field[0]]];
      }
      break;
    case 'default':
      if(array_key_exists($update_field[0], $arr_data)){
        $update_fields_sql[] = "`{$update_field[1]}`= :{$update_field[0]}";
        $update_fields_sql_data[] = [":{$update_field[0]}", $arr_data[$update_field[0]]];
      }
      break;
    case 'image':
      // Проверка/Загрузка изображения
      if (array_key_exists('image', $_FILES)) {
        $max_size = CONFIG['image_file_size_limit'];
        // Проверка размера файла
        if ( !($_FILES['image']['size'] <= $max_size) ) {
          // Формирование и вывод ошибки превышения размера
          $sizeMB = $max_size/1024/1024;
          $result['error'] = "File size is too large (max {$sizeMB}MB)";
          returnResult($result);
        }
        // Проверка типа файла
        $allowed_types = CONFIG['image_allowed_types'];
        $detected_type = exif_imagetype($_FILES['image']['tmp_name']);
        if (!in_array($detected_type, $allowed_types)) {
          $result['error'] = "File type is not supported!";
          returnResult($result);
        }
        // Создание уникального названия и перемещение загруженного файла
        $unic_file_name = uniqid() . '_' . time();
        // Расширение файла
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        // Запоминаем имя файла для сохранения в БД
        $image = $unic_file_name . '.' . $ext;
        // Конечный путь файла
        $uploadfile = CONFIG['upload_dir'] . '/' . $unic_file_name . '.' . $ext;
        // Перемещение загружаемого файла в конечную директорию
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
          $result['error'] = "File upload error!";
          returnResult($result);
        }
        $update_fields_sql[] = "`{$update_field[1]}`= :{$update_field[0]}";
        $update_fields_sql_data[] = [":{$update_field[0]}", $image];
      }
      break;
    case 'authors':
      if(array_key_exists($update_field[0], $arr_data)){
        if(empty($arr_data[$update_field[0]])) {
          $result['error'] = "Field ({$update_field[0]}) not be empty!";
          returnResult($result);
        }
        $authors = $arr_data[$update_field[0]];
        // Проверка авторов
        if (!is_array($authors))
          // Если задан не массив - значит создаём массив из одного автора
          $authors = [$authors];
        // Перебираем всех авторов и проверяем существуют ли таковые ID авторов в БД.
        foreach($authors as $author_id) {
          $mysqli->query("SELECT `{$db_config['column_author_id']}`
            FROM {$db_config['table_authors']}
            WHERE {$db_config['column_author_id']}=:id");
          $mysqli->bind(':id', $author_id);
          $mysqli->execute();
          if($mysqli->rowCount() <= 0) {
            $result['error'] = "Author ID ({$author_id}) not found!";
            returnResult($result);
          }
        }
        $update_fields_sql[] = "`{$update_field[1]}`= :{$update_field[0]}";
        $update_fields_sql_data[] = [":{$update_field[0]}", json_encode($authors)];
      }
      break;
  }
}

// Обновление журнала в БД
$update_fields_sql = implode(', ', $update_fields_sql);
$query = "UPDATE {$db_config['table_magazines']}
 SET {$update_fields_sql}
 WHERE `{$db_config['column_magazine_id']}`=:id";
$mysqli->query($query);
$mysqli->bind(':id', $arr_data['id']);
foreach ($update_fields_sql_data as $field) {
  $mysqli->bind($field[0], $field[1]);
}

if (!$mysqli->execute()) {
  $result['error'] = 'SQL Error!';
} else {
  $result['success'] = true;
}
?>
