<?php

class MyAmzClass {
  public $amzProp1 = 'hey chap !';
  public $amzProp2;
  public function amzMethod1(string|int $mm = '') {}
  public function amzMethod3() {
    //var_dump("I am amz method 3");
  }
  public function amzMethod2() {}
  public function __construct()
  {
    
  }
}

// //var_dump(get_class_methods('MyAmzClass'));
// //var_dump(MyAmzClass::class);
// //var_dump(get_class_methods(MyAmzClass::class));
$o = new MyAmzClass;
// //var_dump(get_class($o));
// //var_dump($o::class);
$my_method =  'amzMethod3';
is_callable([$o, $my_method]) && $o->$my_method();
// //var_dump(is_callable([$o, $my_method], false, $cb_name));
// //var_dump($cb_name);

// //var_dump(get_class_vars(MyAmzClass::class));
// //var_dump(get_parent_class(MyAmzClass::class)); // false

echo "\n=====\n";

// //var_dump(new ReflectionClass(MyAmzClass::class));
print (new ReflectionClass(MyAmzClass::class));

echo "\n=====\n";
class ClassInfo {
  public static function getData(ReflectionClass $class) {
    $details = "";
    $name = $class->getName();

    $details .= $class->isUserDefined() ? "$name is user defined \n" : '';
    $details .= $class->isInstantiable() ? "$name is instantiable\n" : '';

    return $details;
  }
  public static function methodData(ReflectionMethod $method)  {
    return $method->getName();
  }
  
  public static function argData(ReflectionParameter $arg)  {
    $details = "";
    // $declaringClass = $arg->getDeclaringClass(); // prints a lot of info
    $name = $arg->getName();
    $position = $arg->getPosition();

    $details .= "$$name has position $position";

    if($arg->hasType()) {
      $type = $arg->getType();
      $details .= "And the type is $type";
      var_dump($type); // ReflectionUnionType
      if($type instanceof ReflectionUnionType) {
        $types = $type->getTypes(); // arr of ReflectionNamedType objs
        foreach($types as $utype) {
          // var_dump($utype->getName());
        }
      }
    }
    if($arg->isDefaultValueAvailable()) {
      $def = $arg->getDefaultValue();
      var_dump("$name has default value of : $def");
      /*
      Why here allowsNull returns false even though we may not pass
      a value for our method as it has a default value ?
      => From user contributed notes in the docs, if a type is defined,
      null is allowed only if default value is null  
      */
      var_dump($arg->allowsNull()); // false
    }
    return $details;
  }

}
// echo(classInfo::getData(new ReflectionClass(MyAmzClass::class)));

// //var_dump((new ReflectionClass(MyAmzClass::class))->getMethods());

// ReflectionMethod notes

/* The below approach works (which is also called the alternative syntax), but it is deprecated in php 8.4 in favor
of  ReflectionMethod::createFromMethodName("MyAmzClass::__construct")
*/
$reflectionMethod1 = new ReflectionMethod("MyAmzClass::__construct");
// //var_dump($reflectionMethod1);

// This is the recommended approach
$refMethod2 = ReflectionMethod::createFromMethodName("MyAmzClass::__construct");
// //var_dump($refMethod2);

$refMethod3 = new ReflectionMethod(MyAmzClass::class, 'amzMethod2');
// //var_dump($refMethod3);

$refMethod4 = new ReflectionMethod(new MyAmzClass, 'amzMethod1');
//var_dump($refMethod4);
//var_dump(ClassInfo::methodData($refMethod4));

//var_dump($refMethod4->getParameters()); // returns an arr

echo '=====';
$rparam1 = new ReflectionParameter([MyAmzClass::class, 'amzMethod1'], 'mm');
//var_dump($rparam1); // returns ReflectionParameter instance
//var_dump($rparam1->isPassedByReference()); // false since our param is passed by value (not preceeded with an ampersand)
//var_dump(($rparam1->getType())); // returns ReflectionNamedType obj
//var_dump($rparam1->getType()->isBuiltin()); // isBuiltin is a method present on the ReflectionNamedType 

echo "\n=====\n";
echo "This is popps example";
echo "\n=====\n";

$myClass = new ReflectionClass(MyAmzClass::class);
$method = $myClass->getMethod('amzMethod1');
$params = $method->getParameters();
foreach($params as $param) {
  print ClassInfo::argData($param) . "\n";
}

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






