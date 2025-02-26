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
      Why here allowsNull returns false even though we are not forced to pass a value for our method as it has a default value ?
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






