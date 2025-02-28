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
  private $id;
  public $url;
  public $name;
  public $description;
  public $tag;
  /**
    * @var PDO pdo
   */
  private $pdo;
  private $fillable = ['url', 'name', 'description', 'tag'];

  /**
   * @var PDO $pdo
   */
  // The below line causes errors (explanation in the md file)
 // private $pdo = Container::get('ActiveRecordLearningDBPDOConn');
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
  const FIND_BY_ID_QUERY = "SELECT * FROM bookmark WHERE id = ? LIMIT 1;";
  const FIND_BY_DESCRIPTION_QUERY = "SELECT * FROM bookmark WHERE description like ?";

  public function __construct($pdo = null) {
    $this->pdo = $pdo ?? Container::get('ActiveRecordLearningDBPDOConn');
  }
  public function save() {
    $stmt = $this->pdo->prepare(static::INSERT_QUERY);

    // This condition is important for testing , where we return
    // false from prepare() to test our code when the db is not working
    if($stmt) {
      $stmt->execute([
        $this->url,
        $this->name, 
        $this->description, 
        $this->tag
      ]);
      return;
    }
    throw new Exception('An error occurred while trying to connect to DB');
  }
  public function getId() {
    if($this->id) {
      return $this->id;
    }

    $stmt = $this->pdo->prepare("select id from bookmark where url = ?");
    $stmt->execute([$this->url]);
    $res = $stmt->fetch();
    $id = $res['id'];
    return $id;
    // return $this->pdo->lastInsertId();
  }
  public static function create($attributes) {
    // create new instance
    $instance = new static;
    // get intersecting keys
    foreach($attributes as $key => $value) {
      if(in_array($key, $instance->fillable) || $key == 'id') {
        // foreach key assign its value to the corresponding instance property
        $instance->$key = $value;
      }
    }
    // call save method
    $instance->save();
    return $instance;
  }
  /* 
  TODO : I don't know whether to name it find or getOne .

  Mahmoud used getOne in the db internals, while for the model he
  used find (which makes sense) . 
   */
  public static function find($id) {
    $model = new static;
    $stmt = $model->pdo->prepare(static::FIND_BY_ID_QUERY);
    /* Note that you should use fetch or fetchAll as execute()
returns just a boolean */
    $stmt->execute([$id]);
    $res = $stmt->fetch();

    foreach($res as $key => $value) {
      if(in_array($key, $model->fillable))
      $model->$key = $value;
    }
    return $model;
  }
  public static function findByDescription($desc) {
    // query the db
    $pdo = Container::get('ActiveRecordLearningDBPDOConn');
    $stmt = $pdo->prepare(static::FIND_BY_DESCRIPTION_QUERY);
    $stmt->execute(["%$desc%"]);
    // store the res in an array
    $res = $stmt->fetchAll();
    // var_dump('this is res of findByDescription');
    // var_dump($res);

    // create an intermediate array
    $bookmarks_instances = [];
    foreach($res as $row) {
      $instance = new static;
      foreach($row as $key => $value) {
        if(in_array($key, $instance->fillable)) {
          $instance->$key = $value;
        }
      }
      $bookmarks_instances[] = $instance;
    }
    // return the arr of Bookmark instances
    return $bookmarks_instances;
  }
}

