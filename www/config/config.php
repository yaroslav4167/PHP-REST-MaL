<?php
return [
  'db' => [
    'host' => 'localhost',
    'port' => '3306',
    'db_name' => 'magazines_lib',
    'login' => 'root',
    'pass' => '',
    'charset' => 'utf8',

    'table_magazines' => 'magazines',
    'column_magazine_id' => 'id',
    'column_magazine_name' => 'name',
    'column_magazine_date' => 'create_date',
    'column_magazine_description' => 'description',
    'column_magazine_image' => 'image',
    'column_magazine_authors' => 'authors',

    'table_authors' => 'authors',
    'column_author_id' => 'id',
    'column_author_name' => 'name',
    'column_author_patr' => 'patronymic'
  ],
  'upload_dir' => 'uploads',
  'image_file_size_limit' => '2097152', //Bytes
  'image_allowed_types' => [IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF]
]
?>
