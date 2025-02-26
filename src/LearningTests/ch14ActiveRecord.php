<?php

// Here we will follow the singleton pattern
/*
  This is the DB class that is used throughout learning from php 
  architect book through the active record pattern 
*/

// The below line is so important if you will use php
// DatabaseLearning.php because autoloading does not work unless
// your entry point is index.php
use Core\Container;

class DatabaseLearning {
  
  protected static DatabaseLearning|null $instance;
  protected static PDO $connection;

  protected function __construct($pdo = null) {
    try {
      self::$connection = $pdo ?? new PDO(
        'mysql:host=' . '127.0.0.1' . ';dbname=' . 'php_architect_book',
    'root',
      '',
      [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
      );
    } catch(PDOException $e) {
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
    var_dump(DatabaseLearning::getInstance());
    echo "hello from db class";
  }
  public static function pdo() {
    return self::$connection;
  }
}
DatabaseLearning::isDBWorking();

define("BOOKMARK_TABLE_DDL", <<<myQuery

  create table `bookmark` (
  id int not null auto_increment,
  url varchar(255) not null,
  name varchar(255) not null,
  description mediumtext,
  tag varchar(50),
  created datetime not null,
  updated datetime not null,
  primary key (id)
  )

myQuery
);

/* Why did we wrap the table names in backticks ? 
What is the difference between using backticks and using quotes ?

Backticks : wraps tbl and cols names if they have spaces or the
names use the reserved words of sql

quotes : wraps strings . 

Here is a link for a great article by atlassian about these
differences:  https://www.atlassian.com/data/sql/single-double-quote-and-backticks-in-mysql-queries#:~:text=Backticks%20are%20used%20in%20MySQL,SELECT%20%60Album%60.
*/

class Bookmark {
  public $url;
  public $name;
  public $description;
  public $tag;

  /**
   * @var PDO $pdo
   */
  private $pdo = Container::get('ActiveRecordLearningDBPDOConn');
  const INSERT_QUERY = "INSERT INTO bookmark (
      url,
      name,
      description,
      tag,
      created,
      updated
      ) VALUES (
      ?, ?, ?, ?, NOW(), NOW()
      );";
  // const getIdQuery = "";

  public function __construct() {}
  public function save() {
    $stmt = $this->pdo->prepare(static::INSERT_QUERY);
    $stmt->execute([
      $this->url,
      $this->name, 
      $this->description, 
      $this->tag
    ]);
  }
  public function getId() {
    return $this->pdo->lastInsertId();
  }
}

