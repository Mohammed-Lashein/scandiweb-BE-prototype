<?php

use App\Models\Product;
use Core\Database;
use Core\Router;
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require __DIR__ . '/../core/helpers.php';
require_once __DIR__ . "/../vendor/autoload.php";

define("CONFIG_PATH", __DIR__ . '/../config/');

// Database::isDBWorking();

//  var_dump(scandir(CONFIG_PATH));
//  echo "\n";
//  var_dump(array_diff(scandir(CONFIG_PATH), array("..", '.')));

  // echo "<br>";
  // require __DIR__ . "/../routes/web.php";
  // echo "<br>";
  // echo $_SERVER['REQUEST_URI'];
  echo "<br>";
  echo "<br>";
  echo "<br>";
  echo "<br>";
  echo "<br>";
  echo "<br>";
  echo "<br>";
  echo "<br>";

  // Router::resolve();
echo "<pre>";
var_dump(Product::all());
echo "<pre>";

