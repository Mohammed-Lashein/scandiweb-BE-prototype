<?php

use Core\Container;

test("Container class is providing same Database singleton on request", function() {
  $myobj = ['one' => 'two'];
  Container::bind('mySpecificObj', fn() => $myobj);

  $obj1 = Container::get('mySpecificObj');
  $obj2 = Container::get('mySpecificObj');

  // toBe() : ensures that both value refer to the same object
  expect($obj1)->toBe($obj2);
});