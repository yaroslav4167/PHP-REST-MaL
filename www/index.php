<?php
  header('Content-type: application/json');
  define("CONFIG", require_once 'config/config.php');
  $db_config = CONFIG['db'];

  // Check '..' dirs in path
  if( in_array('..', explode('/', $_REQUEST['_url'])) ) {
    http_response_code(401);
    exit;
  }

  include_once 'class/DB.class.php';
  include_once 'functions.php';
  $mysqli = new DB();
  $body_data = file_get_contents('php://input');

  $api_file = 'api'.$_REQUEST['_url'].'.php';
  if (file_exists($api_file))
    include $api_file;

  if (isset($result))
    echo json_encode($result);
?>
