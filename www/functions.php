<?php
/**
 *  Проверка является ли строка json-данными
 * @param  mixed  $string               Проверяемая строка
 * @return bool         строка является json-объектом?
 */
function isJson($string):bool {
   json_decode($string);
   return json_last_error() === JSON_ERROR_NONE;
}

/**
 * Проверка наличия всех ключей в массиве
 * @param  array  $keys               Массив ключей для поиска
 * @param  array  $arr                Проверяемый массив
 * @return bool       Существуют ли все заданные ключи в массиве?
 */
function arrayNeedleKeyCheck(array $keys, array $arr):bool {
  foreach ($keys as $key) {
    if(!array_key_exists($key, $arr))
      return false;
  }
  return true;
}

/**
 * Возвращение результата и завершение работы скрипта
 * @param  array $result               Массив для конвертирования в JSON и выдачи
 */
function returnResult(array $result):void {
  echo json_encode($result);
  exit;
}
?>
