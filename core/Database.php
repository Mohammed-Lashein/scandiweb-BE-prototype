<?php

// Here we will follow the singleton pattern

/* TODO : Search about where will the db instance be instantiated 
(search the codebase not on the internet . I thought maybe in the container but it was not there)*/

namespace Core;

class Database {
  /* 
  $instance has type  Database|null instead of just Database so that 
  on testing I can use ReflectionClass and reset the $instance value
  to ensure that every test deals with a separate singleton
   */
  protected static Database|null $instance;
  protected static \PDO $connection;

  protected function __construct($pdo = null) {
    try {
      self::$connection = $pdo ?? new \PDO(
        'mysql:host=' . Config::get('database.host') . ';dbname=' . Config::get('database.database'),
       Config::get('database.username'),
        Config::get('database.password'),
        [\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC]
      );
    } catch(\PDOException $e) {
      /* Using a return here is frowned upon because a constructor should not return anything */
      var_dump($e->getMessage());
      return $e->getMessage();
      // Never die a script . I kept more than 30mins chasing this issue.
      // die($e->getMessage());
    }
  }
  public static function getInstance() {
    if(!isset(self::$instance)) {
      /* TODO : go understand the difference between new self and new static
      */
      self::$instance =  new self;
    }
    return self::$instance;
  }

  public static function isDBWorking() {
    var_dump(Database::getInstance());
    echo "hello from db class";
  }
  public static function pdo() {
    return self::$connection;
  }
}
// ensuring that the db is working .. not for production
// $db =  Database::getInstance();
// $db->isDBWorking();