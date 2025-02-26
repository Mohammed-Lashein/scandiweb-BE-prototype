<?php

require __DIR__ . '/../../src/LearningTests/ch5Registry.php';

describe("Registry pattern tests", function() {

  $reg = Registry::getInstance();
  test("Registry is a singleton", function() use ($reg) {
    // toBe() ensures that both vars refer to the same obj
    expect($reg)->toBe(Registry::getInstance());
  });

  test("returns false on providing invalid key", function() use ($reg) {
    expect($reg->isValid('key'))->toBeFalse();
  });

  test("returns null on providing invalid key", function() use ($reg) {
    expect($reg->get('key'))->toBeNull();
  });

  test("returns the value of a set registry key", function()  use ($reg){
    $reg->set('key', 'amazing key value');
    expect($reg->isValid('key'))->toBeTrue();
  });
});