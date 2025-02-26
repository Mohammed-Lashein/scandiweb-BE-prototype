<?php

namespace App\Models;
use Core\Model;

ini_set('display_errors', 1);

class Product extends Model {
  static $table = 'products';

  public static function all() {
    /* Since we initialized $pdo in the Model constructor (which
    we had no choice but to do that to avoid the error I talked
    about in ch14ActiveRecord.md) , we need to create an instance
   of the Model so we can access $pdo */
    $model = new static;
    $fetchAll_Query = "SELECT * from ";
    $fetchAll_Query .= static::$table;
    $fetchAll_Query .= " LIMIT 20;";
    $res = $model::$pdo->query($fetchAll_Query)->fetchAll();
    return $res;
  }
}
