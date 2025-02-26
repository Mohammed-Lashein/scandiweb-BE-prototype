<?php

namespace Core;

use Core\Exceptions\EntryNotFoundException;

class Container {

  private static array $entries;
  
  // public function set
  // public function bind($abstract, $concrete)
  /* using $abstract and $concrete follows laravel conventions (In the src/Illuminate/Container/Container.php) . However, since here we are 
  just dealing with cb, we can use more meaningful names .  */
  public static function bind($class, $callable) {
    static::$entries[$class] = $callable;
  }
  public static function get($id) {
    // check if we have an entry
    if(static::has($id)) {
      $callableAssocWithId = static::$entries[$id];
      return $callableAssocWithId();
    }
    throw new EntryNotFoundException("Entry of id : $id is not available in the Container");
  }
  public static function has(string $id): bool {
    return isset(static::$entries[$id]);
  }
}