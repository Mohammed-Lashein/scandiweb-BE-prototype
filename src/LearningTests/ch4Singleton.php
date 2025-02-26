<?php

class DbConn {
  private static $instance;
  private function __construct(){}
  public static function getInstance() {
    if(!isset(self::$instance)) {
      self::$instance = new DbConn();
    }
    return self::$instance;
  }
}