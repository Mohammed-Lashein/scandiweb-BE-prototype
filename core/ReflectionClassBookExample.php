<?php

// === Using Reflection API (a real world example from the book) ===
class Person {
  public $name;
  public function __construct($name) {
    
  }
}
interface Module {
  public function execute();
}
class FtpModule implements Module {
  public function setHost(string $host) {
    print "FtpModule::setHost(): $host\n";
  }
  public function setUser(string|int $user) {
    print "FtpModule::setUser(): $user\n";
    
  }

  public function execute() {

  }
}
class PersonModule implements Module {
  public function setPerson(Person $person) {
    print "PersonModule::setPerson(): {$person->name}\n";
  }
  public function execute() {

  }
}
class ModuleRunner {
  private $configData = [
    PersonModule::class => [
      'person' => 'matt zandstra',
    ],
    FtpModule::class => [
      'host' => 'example.com',
      'user' => 'laracasts'
    ]
  ];

  private $modules =[];

  public function handleMethod(Module $module, ReflectionMethod $method, array $params) {
    /* naming changes 
    $params should be $paramsAndArgs
    $args should be $parameters
     */

    $name = $method->getName();
    $args = $method->getParameters();
    if(count($args) != 1 || substr($name, 0, 3) != 'set') {
      return false;
    }

    $property = strtolower(substr($name, 3)); // eg: host, user

    // if user does not exist in configData as a key, then return false.
    if(!isset($params[$property])) {
      return false;
    }

    if(!$args[0]->hasType()) { 

      // If the passed arg does not have a pre-defined type , we assume that is a primitive . 

      $method->invoke($module, $params[$property]); // invoke a reflected method which belongs to an obj, and pass to it the param

      // The above code will do eg : 
      // $ftpModule->setHost('example.com')
      return true;
    }

    $arg_type = $args[0]->getType();
    if( 
      !($arg_type instanceof ReflectionUnionType) && class_exists($arg_type->getName())
    ) {
      /* 
      If $arg_type is not an instance of ReflectionUnionType 
      AND a class_exists for $arg_type->getName()
      */

      $method->invoke(
        $module,
        (new ReflectionClass($arg_type->getName()))->newInstance($params[$property])
      );
    } else {
      $method->invoke($module, $params[$property]);
    }

    return true;
  }



  public function init() {
    $interface = new ReflectionClass(Module::class);
    foreach($this->configData as $module_name => $params) {
      $module_class = new ReflectionClass($module_name);
      if(!$module_class->isSubclassOf($interface)) {
        // method desc from docs : Checks if the class is a subclass of a specified class or implements a specified interface.
        throw new Exception("Unkown module type: $module_name");
      }

      /* 
        A great advice from popps book (And also mahmoud implemented it): (for production code, be sure to code defensively: check that the constructor method for each Module object doesn’t require arguments before creating an instance).
       */
      $module = $module_class->newInstance();
      foreach($module_class->getMethods() as $method) {
        $this->handleMethod($module, $method, $params);
      }
      array_push($this->modules, $module);
    }
  }
}

$test = new ModuleRunner();
$test->init();
