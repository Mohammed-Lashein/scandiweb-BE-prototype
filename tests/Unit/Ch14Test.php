<?php

use Core\ActiveRecordLearningDB;
use Core\Container;

require __DIR__ . '/../../src/LearningTests/ch14ActiveRecord.php';


Container::bind('ActiveRecordLearningDBPDOConn', function() {
  $conn = ActiveRecordLearningDB::getInstance();
  return $conn::pdo();
});

beforeEach(function() {
  /**
   * @var PDO $pdo
   */
  $pdo = Container::get('ActiveRecordLearningDBPDOConn');
  $pdo->query('drop table bookmark');
  $pdo->query(BOOKMARK_TABLE_DDL);
});

describe("bookmark table reset", function() {
  /**
   * @var PDO $pdo
   */
  $pdo =  Container::get('ActiveRecordLearningDBPDOConn');
  test("no previous records are present in the table ",function() use($pdo){
    
    $count = $pdo->query('select count(*) from bookmark')->fetchColumn();
    $count = (int) $count;
    expect($count)->toEqual(0);
  });

  test("the table has expected schema even after data reset", function() use($pdo){
    $desc = $pdo->query("describe bookmark")->fetchAll(PDO::FETCH_COLUMN);
    $keys_arr = [
    'id',
    'url',
    'name',
    'description',
    'tag',
    'created',
    'updated'
    ];
    foreach($keys_arr as $key) {
      expect($key)->toBeIn($desc);
    }
  });

})->only();

test("create a new Bookmark link", function() {
  $link = new Bookmark;
  $link->url = 'https://google.com';
  $link->name = 'google';
  $link->description = 'google link';
  $link->tag = 'search engine';

  $link->save();
  expect($link->getId())->toEqual(1);
});
