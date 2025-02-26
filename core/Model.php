<?php

namespace Core;

class Model {
  protected static $table;
  protected static $pdo;
  public function __construct() {
    // won't work now bec we are using php architect conn
    // $conn = Container::get("Database");
    /* !!!!
    May cause errors since $pdo is private while the
    constructor is public !!!! 
    */
    $conn = ActiveRecordLearningDB::getInstance();

    static::$pdo = $conn::pdo();
  }

  // Obsolete since in scandiweb task, we won't create any
  // products but instead work with existing data 
  /* 
    Also, I won't create a db factory because it is not rational
    that with each api call the db gets created from scratch .
    So, most of our work will be reading data
  */
  // public function save() {
    // get cols names using pdo
    // add each col name to the insert stmt
  // }
  public static function all() {

  }
  public static function findOrFail() {

  }
}