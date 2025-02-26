<?php

/* 
  This file is a good example for mocking . 
  I was not intending to create it, but when I wanted to test the Container functionality, I kept getting errors . 

  After debugging, I found that the problem was with the Database class
  because it was trying to use PDO while I was not connected to mysql . 

  So I went and created a DatabaseTestWrapper in order to be able to 
  mock PDO and test Database class effectively (without connecting to an actual database). 

  One Last note to mention: Using a wrapper class to be able to test
  another class was mentioned in Ch6 Mock objects in php architect book .
*/

use Core\Database;

class DatabaseTestWrapper extends Database {
  public static function providePdoMock($pdo = null) {
   return new self($pdo);
  }
}

// Reset singleton before each test
beforeEach(function() {

  /* The below code was suggested by chat, but after commenting it I 
  found that it did not affect the tests . 
  
  I am keeping it here because it has a note or 2 about ReflectionClass
  */

  // $reflection = new ReflectionClass(DatabaseTestWrapper::class);
  // $instance_property = $reflection->getProperty('instance');
  /* 
  As of php 8.1 this method has no effect; all properties
  are accessible by default . 
   */
  // $instance_property->setAccessible(true);
  // $instance_property->setValue(null, null);

  
});
describe("Database class tests", function() {
  test("DatabaseTestWrapper::getInstance returns the same instance", function() {
    $mockPDO = Mockery::mock(PDO::class);
    DatabaseTestWrapper::providePdoMock($mockPDO);
    $db1 = DatabaseTestWrapper::getInstance();
    $db2 = DatabaseTestWrapper::getInstance();

    expect($db1)->toBe($db2);
  });

  test("Database instance contains the mock pdo", function() {
    $mockPDO = Mockery::mock(PDO::class);
    DatabaseTestWrapper::providePdoMock($mockPDO);
    $db = DatabaseTestWrapper::getInstance();
    expect($db->pdo())->toBe($mockPDO);
  });
});

// TODO
// Tests for the actual PDO connection 