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

  // Удаление элемента из БД
  $query = "DELETE FROM {$db_config['table_authors']}
   WHERE `{$db_config['column_author_id']}`=:id";
  $mysqli->query($query);
  $mysqli->bind(':id', $arr_data['id']);

  if (!$mysqli->execute()) {
    $result['error'] = 'SQL Error!';
  } else {
    $result['success'] = true;
  }
?>
