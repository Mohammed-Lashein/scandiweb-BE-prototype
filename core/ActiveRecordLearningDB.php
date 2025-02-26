<?php

// Here we will follow the singleton pattern

/*
  This is the DB class that is used throughout learning from php 
  architect book through the active record pattern 
*/

namespace Core;
// The below line is so important if you will use php
// DatabaseLearning.php because autoloading does not work unless
// your entry point is index.php
require __DIR__ . '/Config.php';

class ActiveRecordLearningDB {
  
  protected static ActiveRecordLearningDB|null $instance;
  protected static \PDO $connection;

  protected function __construct($pdo = null) {
    try {
      // self::$connection = $pdo ?? new \PDO(
      //   'mysql:host=' . Config::get('database.host') . ';dbname=' . 'php_architect_book',
      //  Config::get('database.username'),
      //   Config::get('database.password'),
      //   [\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC]
      // );
      self::$connection = $pdo ?? new \PDO(
        'mysql:host=' . '127.0.0.1' . ';dbname=' . 'php_architect_book',
       Config::get('database.username'),
        Config::get('database.password'),
        [\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC]
      );
    } catch(\PDOException $e) {
      /* Using a return here is frowned upon because a constructor should not return anything */
      var_dump($e->getMessage());  
    }
  }
  public static function getInstance() {
    if(!isset(self::$instance)) {
      self::$instance =  new self;
    }
    return self::$instance;
  }

  public static function isDBWorking() {
    var_dump(ActiveRecordLearningDB::getInstance());
    echo "hello from db class";
  }
  public static function pdo() {
    return self::$connection;
  }
}
// ActiveRecordLearningDB::isDBWorking();

