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

  if (!arrayNeedleKeyCheck(['id'], $arr_data)) {
    $result['error'] = 'Required key ("id") not found!';
    returnResult($result);
  }

  $update_fields = [
    // [field_sql_name, json_key, type]
    ['name', $db_config['column_author_name'], 'needle'],
    ['patronymic', $db_config['column_author_patr'], 'default']
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
      }
    }

  // Обновление журнала в БД
  $update_fields_sql = implode(', ', $update_fields_sql);
  $query = "UPDATE {$db_config['table_authors']}
   SET {$update_fields_sql}
   WHERE `{$db_config['column_author_id']}`=:id";
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
