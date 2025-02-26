<?php

namespace Core;

// to enable testing correctly 
if(!defined('CONFIG_PATH')) {
define("CONFIG_PATH", __DIR__ . '/../config/');
}
class Config {
  private static array $config = [];
  private static function setConfigData() {
    if(count(static::$config) == 0) {
    /*
    using array_diff to remove  the . and .. present in unix
    environments . This advice was in scan_dir page in php.net 
    */
    $config_entries = array_diff(scandir(CONFIG_PATH), array('..', '.'));

    foreach($config_entries as $entry) {
      $config_key = str_replace('.php', '', $entry);
      if(is_dir(CONFIG_PATH . $entry)) {
        continue;
      } 
      static::$config[$config_key] = require CONFIG_PATH . $entry;
    }
    }
  }

  public static function get(string $key, $default = null) {
    if(count(static::$config) === 0) {
      static::setConfigData();
    }
      $config = static::$config;
      foreach (explode('.', $key) as $segment) {
          if (!isset($config[$segment])) {
              return $default;
          }
          $config = $config[$segment];
      }
      return $config;
  }
}