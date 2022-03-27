<?php
  if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    exit;
  }

  if (!isJson($_POST['data'])) {
    $result['error'] = 'Input data is not a valid JSON!';
    returnResult($result);
  }

  $body_data = $_POST['data'];
  $arr_data = json_decode($body_data, true);

  if (!arrayNeedleKeyCheck(['name', 'authors'], $arr_data)) {
    $result['error'] = 'One of required keys ("name", "authors") not found!';
    returnResult($result);
  }

  // Задаём изначальные значения
  // Необязательные значения проверяем на существование в входящих данных
  $name = $arr_data['name'];
  $authors = $arr_data['authors'];
  $description = array_key_exists('description', $arr_data)?$arr_data['description']:'';
  $image = '';
  $date = array_key_exists('date', $arr_data)?$arr_data['date']:'';

  // Проверка даты
  if (!empty($date) && !strtotime($date)) {
    $result['error'] = "Input date must be a valid datetime!";
    returnResult($result);
  }

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
  }

  // Добавление журнала в БД
  $query = "INSERT INTO {$db_config['table_magazines']}
   (`{$db_config['column_magazine_name']}`,
     `{$db_config['column_magazine_date']}`,
     `{$db_config['column_magazine_description']}`,
     `{$db_config['column_magazine_image']}`,
     `{$db_config['column_magazine_authors']}`
   )
   VALUES (:name, :mdate, :descr, :image, :authors)";
  $mysqli->query($query);
  $mysqli->bind(':name', $name);
  $mysqli->bind(':mdate', $date);
  $mysqli->bind(':descr', $description);
  $mysqli->bind(':image', $image);
  $mysqli->bind(':authors', json_encode($authors));

  if (!$mysqli->execute()) {
    $result['error'] = 'SQL Error!';
  } else {
    $result['success'] = true;
  }
?>
