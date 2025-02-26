<?php

namespace Core;

class Router {
  private static $routes;

  public static function addRoute($method, $path, $handler) {
    static::$routes[$method][$path] = $handler;
  }
  public static function get($path, $handler) {
    static::addRoute("GET", $path, $handler);
  }
  public static function post($path, $handler) {
    static::addRoute("POST", $path, $handler);
  }

  public static function resolve() {
    $reqMethod = $_SERVER['REQUEST_METHOD'] ?? "GET";
    $reqPath = $_SERVER['REQUEST_URI'] ?? '/';
    foreach(static::$routes as $route) {
      // $route = GET or POST etc ...
      if(isset($route[$reqPath])) {
        [$className, $method] = $route[$reqPath];
        $className::$method();
      }
    }
  }
}

