<?php

namespace Core;

use Core\Exceptions\EntryNotFoundException;
use ReflectionClass;

class Container {

  private static array $entries;

  private $my_entries = [
    'Database' => fn() => Database::getInstance()
  ];

  // public function set
  // public function bind($abstract, $concrete)
  /* using $abstract and $concrete follows laravel conventions (In the src/Illuminate/Container/Container.php) . However, since here we are 
  just dealing with cb, we can use more meaningful names .  */
  public static function bind($class, $callable) {
    $entries[$class] = $callable;
  }
  public static function get($id) {
    // check if we have an entry
    if(static::has($id)) {
      $callableAssocWithId = static::$my_entries[$id];
      return $callableAssocWithId();

      /* An important note for you: Mahmoud gave the get method more
      than its responsibilities . He gave it the ability to use resolve
      and return the newly created $concrete (remember we have $abstract and $concrete) . 
      
      But after inspecting laravel code, I found that they threw an EntryNotFoundException (psr compliant after inspecting code) and this is the correct thing to do . 

      get() is not supposed to resolve a new $concrete at all . 
       */
      throw new EntryNotFoundException("Entry of id : $id is not available in the Container");

    } else {
      // if not, then instantiate it (using resolve)
      return static::resolve($id);
    }
  }
  public static function has(string $id): bool {
    return isset($my_entries[$id]);
  }

  public static function resolve(string $id) {
    // TODO : resolve the class for us (or closure ... I still don't understand)
    $refClass = new ReflectionClass($id);
  }
}