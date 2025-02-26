<?php

/* 
  Pattern explanation : 
  - This pattern is like the context in React, where instead of prop
  drilling we can just ask for the data we need from the context
*/

class Registry {
  private static $instance;
  private static $registry_array = [];
  private function __construct(){}
  public static function getInstance() {
    if(!isset(self::$instance)) {
      self::$instance = new Registry();
    }
    return self::$instance;
  }

  public function isValid($key) {
    return array_key_exists($key, static::$registry_array);
  }
  public function get($key) {
    return static::$registry_array[$key] ?? null;
  }
  public function set($key, $value) {
    static::$registry_array[$key] = $value;
  }
}

/* 
  A great note from the book : 
  Tip
  When you integrate a design pattern into your code, the name of your class should still reflect it’s role or function in your application, not necessarily the pattern’s name.

  Referring to code using a pattern name is good for communication with programmers outside of your project; within your project, however, the names of your classes should be appropriate to the domain of your application and be well understood by your colleagues.

  Throughout the rest of this chapter the example class names reflect the patterns name and the specific implementation being developed, not a role in an application. This is done for clarity of the example, not as an example of a good naming convention.

*/

class DbConnections extends Registry {
  /* This class will help us connect to 3 dbs without creaa */
}
class MysqlConnection {

}
$dbc = DbConnections::getInstance();
$dbc->set(
  'contact',
  new MysqlConnection('user1','pass1','db1','host1')
);
$dbc->set(
  'orders',
  new MysqlConnection('user2','pass2','db2','host2')
);
$dbc->set(
  'archives',
  new MysqlConnection('user3','pass3','db3','host3')
);

// Domain model classes
class Customer {
  public $db;
  public function __construct() {
    $dbc = DbConnections::getInstance();
    $this->db = $dbc->get('contacts');
  }
}
class Orders {
  public $db_current;
  public $db_history;
  public function __construct() {
    $dbc = DbConnections::getInstance();
    $this->db_current = $dbc->get("orders");
    $this->db_history = $dbc->get("archives");
  }
}

/*
  The below code is to learn more about the $GLOBALS super global .

  In PHP 8.1, its behavior changed so that we can't change the value of
  an existing key in it . 

  i.e $GLOBALS is a read only var . 

  $a = 1;
  $my_globals = $GLOBALS;
  $my_globals[$a] = 2;

  echo $my_globals[$a] // 2
  echo $GLOBALS[$a] // 1

 */
// $GLOBALS['key'] = 'mykey';
// var_dump($GLOBALS);
