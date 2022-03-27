<?php
  if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    exit;
  }

  if (!empty($body_data) && !isJson($body_data)) {
    $result['error'] = 'Input data is not a valid JSON!';
    returnResult($result);
  } elseif (empty($body_data)) {
    $body_data = '[]';
  }

  $arr_data = json_decode($body_data, true);

  // Формируем запрос на вывод списка данных из БД
  $query = "SELECT * FROM {$db_config['table_magazines']} LIMIT :page, :perPage";
  // Если пользователь задал лимиты - устанавливаем их
  $page = 0;
  $per_page = 500;
  if (array_key_exists('page', $arr_data)) {
    $page = $arr_data['page'];
  }
  if (array_key_exists('perPage', $arr_data)) {
    $per_page = $arr_data['perPage'];
  }
  $mysqli->query($query);
  $mysqli->bind(':page', (int)$page);
  $mysqli->bind(':perPage', (int)$per_page);

  if (!$mysqli->execute()) {
    $result['error'] = 'SQL Error!';
  } else {
    $result['success'] = true;
    $result['content'] = $mysqli->resultset();
  }
?>
