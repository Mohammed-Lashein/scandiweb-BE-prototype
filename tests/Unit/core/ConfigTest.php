<?php

use Core\Config;

test("Config class is reading config data correctly", function() {
  define('CONFIG_PATH', __DIR__ . '/../../../config/');

  expect(Config::get('database.username'))->toEqual('root');
  expect(Config::get('database.host'))->toEqual('localhost');
  expect(Config::get('database.database'))->toEqual('scandiweb-task');
  expect(Config::get('database.password'))->toEqual('');
});