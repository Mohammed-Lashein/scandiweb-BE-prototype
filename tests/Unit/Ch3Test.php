<?php

require __DIR__ . '/../../src/LearningTests/ch3Factory.php';

// Note that the filename should end with Test (case-sensitive) or the test won't run 
test("aloha", fn() => expect(true)->toBeTrue());
describe("Color factory tests", function() {
  $color_factory = new Color;
  test("test factory instantiation", function() use ($color_factory){
    expect($color_factory)->toBeInstanceOf(Color::class);
  });

  test("factory contains getRgb method", function() use ($color_factory){
    expect(method_exists($color_factory, 'getRgb'))->toBeTrue();
  });

  test("getRgb white", function(){
    $white = new Color(255,255,255);
    expect($white->getRgb())->toEqual('#ffffff');
  }); 

  test("getRgb red", function() {
    $red = new Color(255,0,0);
    expect($red->getRgb())->toEqual('#ff0000');
  });

  test("getRgb random color", function() {
    $color = new Color(rand(0,255),rand(0,255),rand(0,255));
    expect($color->getRgb())->toMatch('/^#[0-9a-f]{6}$/');

    /* Regex explained : /^#[0-9a-f]{6}$/
      ^ caret : An anchor denoting that the matched pattern should start
      with #
      [0-9a-f] : character class 
      {6} : A quantifier that specifies exactly 6 chchs matching the prev pattern
      $ : another anchor, denoting that the string must end after reaching six characters
    */

    $color2 = new Color($t = rand(0,255), $t, $t);
    expect($color2->getRgb())->toMatch('/^#([0-9a-f]{2})\1\1$/');

    /* 
      /^#([0-9a-f]{2})\1\1$/
      ^ : start of string
      # : literal match
      ([0-9a-f]{2}) : A capturing group matching exactly 2 hex chars
      \1 : A backreference to what the capturing group caught 
      $ : End of the string

      Generates a monochromatic color (all RGB values are the same)
    */
  });

  test("color cannot be less than 0", function() {
    // $color = new Color(-1);
    $color = new Color(-1);
    $color->getRgb();
    /* 
      PCRE : Perl Compatible Regex
    */

    /* 
      Since I cannot access the err msg directly, then the best option would be to expect exactly what the err msg will be . 

      How will I be able to do that without regex ?
      => You passed the param to the constructor, so you know how will the err msg look like
     */
  })->throws(InvalidArgumentException::class, "The color: -1 is out of bounds 0 and 255");

  test("color cannot be greater than 255", function() {
    $color = new Color(256);
    $color->getRgb();
  })
  ->throws(InvalidArgumentException::class, 'The color: 256 is out of bounds 0 and 255');

  test("get color by name", function() {
    $factory = new CrayonBox;
    $red = $factory->getColor('red');
    expect($red)->toBeInstanceOf(Color::class);
    expect($red->getRgb())->toEqual('#ff0000');

    $lime = $factory->getColor('LIme');
    expect($lime)->toBeInstanceOf(Color::class);
    expect($lime->getRgb())->toEqual('#00ff00');
  });

  // test("invalid color name returns black", function() {
  //   $factory = new CrayonBox;
  //   $lemon = $factory->getColor('lemon');
  //   expect($lemon->getRgb())->toEqual('#000000');
  // });
  // ->throws(Error::class, "lemon is an invalid color");
});

describe("Monopoly adv stuff", function() {
  test("test propertyInfo param obj", function() {
    $list = array("type", 'price', 'color', 'rent');
    $test_prop = new PropertyInfo($list);
    expect($test_prop)->toBeInstanceOf(PropertyInfo::class);
    foreach($list as $prop) {
      expect($prop)->toEqual($test_prop->$prop);
      /*  expect('type')->toEqual('type') bec $test_prop->$prop is like
      $test_prop->type = 'type'
       */
    }
  });

  test("propertyInfo param obj works with less params provided", function() {
    $list = array("type", 'price',);
    $test_prop = new PropertyInfo($list);
    expect($test_prop)->toBeInstanceOf(PropertyInfo::class);
    foreach($list as $prop) {
      expect($prop)->toEqual($test_prop->$prop);
    }
    expect($test_prop->color)->toBeNull();
    expect($test_prop->rent)->toBeNull();
  });

  test("get prop info return", function() {
    $assessor = new TestableAssessor;
    expect($assessor->getPropInfo('Boardwalk'))->toBeInstanceOf(PropertyInfo::class);
    expect($assessor->getProperty('Boardwalk'))->toBeInstanceOf(Street::class);
  });

  test("Throws an exception on passing invalid property name", function() {
    $assessor = new TestableAssessor;
    expect($assessor->getPropInfo("pyramids"));
  })->throws(InvalidArgumentException::class, 'pyramid');

  /* I used the below example to demonstrate that the 2nd arg passed 
  to throws() will be validated against regex not full match (the
  passed string will be searched for in the $exceptionMessage instead of strict equality) */
  // ->throws(InvalidArgumentException::class, 'pudding');
});