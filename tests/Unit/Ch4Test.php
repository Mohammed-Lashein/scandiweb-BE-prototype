<?php

require __DIR__ . '/../../src/LearningTests/ch4Singleton.php';

describe("singleton tests", function() {
  test("same instance returned", function() {
    $instance1 = DbConn::getInstance();
    $instance2 = DbConn::getInstance();

    // toBe() ensures that both vars refer to the same obj . 
    expect($instance1)->toBe($instance2);
  });

  test("directly instantiating a singleton throws an err", function() {
    $db = new DbConn;
    expect($db)->toBeInstanceOf(DbConn::class);
  })->throws(Error::class);
});
