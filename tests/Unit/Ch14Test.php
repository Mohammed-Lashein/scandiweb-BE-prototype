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
afterEach(function() {
  Mockery::close();
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

});

test("create a new Bookmark link", function() {
  $link = new Bookmark;
  $link->url = 'https://google.com';
  $link->name = 'google';
  $link->description = 'google link';
  $link->tag = 'search engine';

  $link->save();
  expect($link->getId())->toEqual(1);

  $pdo = Container::get("ActiveRecordLearningDBPDOConn");
  $res = $pdo->query('select * from bookmark')->fetchAll();

  expect(count($res))->toEqual(1);

  foreach(['url', 'name', 'description', 'tag'] as $key) {
    /* 
      It is super important to use $res[0][$key] not just
      $res[$key] as we will get an array containing each row from
      the db as an array (we have a multidimentional array)
    */
    expect($link->$key)->toEqual($res[0][$key]);
  }
});

test("create multiple bookmark links manually", function() {
  $link1 = new Bookmark;
  $link1->url = 'https://yahoo.com';
  $link1->name = 'yahoo';
  $link1->description = 'yahoo link1';
  $link1->tag = 'like facebook';

  $link1->save();
  expect($link1->getId())->toEqual(1);
  
  $link2 = new Bookmark;
  $link2->url = 'https://facebook.com';
  $link2->name = 'facebook';
  $link2->description = 'facebook link2';
  $link2->tag = 'fb made react and gql';

  $link2->save();
  expect($link2->getId())->toEqual(2);

  /* 
      expect($link1->getId())->toEqual(1);
      expect($link2->getId())->toEqual(2);
  */
});
test("create multiple bookmarks using create static method", function() {
  $link1 = Bookmark::create([
    'url' => 'gql.com',
    'name' => 'gql',
    'description' => 'gql is harder to learn than rest',
    'tag' => 'fb'
  ]);

  $link2 = Bookmark::create([
    'url' => 'hasura.com',
    'name' => 'hasura',
    'description' => 'hasura is harder to learn than rest',
    'tag' => 'hasura inc'
  ]);
  
  expect($link1->name)->toEqual('gql');
  expect($link2->name)->toEqual('hasura');
});
test("find created bookmark by id", function() {
  Bookmark::create([
    'url' => 'gql.com',
    'name' => 'gql',
    'description' => 'gql is harder to learn than rest',
    'tag' => 'fb'
  ]);
  $link = Bookmark::find(1);

  expect($link)->toBeInstanceOf(Bookmark::class);
  expect($link->name)->toBe('gql');
  expect($link->getId())->toEqual(1);
});

test("DB fails gracefully", function() {
  $mockedDB = Mockery::mock((Container::get("ActiveRecordLearningDBPDOConn"))::class);

  $mockedDB
  ->shouldReceive('prepare')
  ->andReturn(false);


  $link = new Bookmark($mockedDB);

  $link->save();
  /*
  In pest, the 2nd param for throws() is a string to search
  for in the Error or Exception message . It needn't be an exact
  match .  
  */
})->throws(Exception::class, "An error occurred");

test("Find bookmarks by description", function() {
  Bookmark::create([
    'name' => 'max blog',
    'url' => 'https://frontendatscale.com',
    'description' => 'good FE blog ',
    'tag' => 'FE'
  ]);
  
  Bookmark::create([
    'name' => 'kp website',
    'url' => 'https://kevinpowell.com',
    'description' => 'good css blog ',
    'tag' => 'css'
  ]);
  $res = Bookmark::findByDescription('blog');

  // var_dump('res from test');
  // var_dump($res[0]->getId());
  // var_dump($res);

  expect(count($res))->toBe(2);
  expect($res[0]->getId())->toBe(1);
  expect($res[1]->tag)->toBe('css');
});
