<?php

$name = 'scandiweb task';
$db = 'mysql';

$todo = $name . $db;
var_dump($todo);

function hello() {
  echo "hello there";
}
hello();
var_dump(
  [
    'hello' => 'aloha world',
    2025 => 'a difficult year !!',
    'nested' => [
      'I am nested !'
    ]
    ]
);

// var_dump(php_ini_loaded_file());
echo "<br>";
// var_dump(php_ini_scanned_files());
xdebug_info();
// phpinfo();




