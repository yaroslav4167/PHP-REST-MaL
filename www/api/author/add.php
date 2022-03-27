<?php
  if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    exit;
  }

  if (!isJson($body_data)) {
    $result['error'] = 'Input data is not a valid JSON!';
    returnResult($result);
  }

  $arr_data = json_decode($body_data, true);

  if (!arrayNeedleKeyCheck(['name'], $arr_data)) {
    $result['error'] = 'Required key ("name") not found!';
    returnResult($result);
  }

  // Задаём изначальные значения
  // Необязательные значения проверяем на существование в входящих данных
  $name = $arr_data['name'];
  $patronymic = array_key_exists('patronymic', $arr_data)?$arr_data['patronymic']:'';

  $db_config = CONFIG['db'];
  $query = "INSERT INTO {$db_config['table_authors']}
   (`{$db_config['column_author_name']}`, `{$db_config['column_author_patr']}`)
   VALUES (:name, :patronymic)";
  $mysqli->query($query);
  $mysqli->bind(':name', $name);
  $mysqli->bind(':patronymic', $patronymic);

  if (!$mysqli->execute()) {
    $result['error'] = 'SQL Error!';
  } else {
    $result['success'] = true;
  }
?>
