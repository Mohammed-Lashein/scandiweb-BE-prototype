<?php

require __DIR__ . '/../../src/LearningTests/ch2.php';

/* A note regarding BadDollar tests : 
  - The writer wrote the tests in a sequence that would explain the 
  problem we are having . 
  - But I wrote it in terms of logical sequence . That made the problem
  less ovbvious (as now salary of emp2 is 400 when the code reaches emp2 part) 
 */

describe("work salaries", function() {
  $job = new Work();

  describe("emp 1 tests", function() use ($job) {
    $p1 = new Person;
    test("add salary to emp1", function() use ($job, $p1) {
      $p1->wallet = $job->payDay();
      expect($p1->wallet->getAmount())->toEqual(200);
    });

    test("inc emp1 salary", function() use ($job, $p1) {
        /* 
  This line will cause a change in $p1 and also $p2 . 
  Why ? 
  => Because their wallets are sharing the same $job instance, which 
  has instantiated only 1 #amount property . 
  */

      $p1->wallet->add($job->payDay());
      expect($p1->wallet->getAmount())->toEqual(400);
    });
  
  });

  describe("emp 2 tests", function() use ($job) {
    $p2 = new Person;
    // test("add salary to emp2", function() use ($job, $p2) {

    //   $p2->wallet = $job->payDay();
    //   expect($p2->wallet->getAmount())->toEqual(200);
    // });
    
    // test("emp2 should not be affected by emp1 salary increase", function() use ($p2) {
    //   expect($p2->wallet->getAmount())->toEqual(200); // will fail
    // });
  });

  // test("work salary should not change", function() use ($job) {
  //   expect($job->payDay()->getAmount())->toEqual(200);
  // });
});

describe("Monopoly game", function() {
  $game = new Monopoly();
  describe("player initialization tests",function() use ($game) {
    $p1 = new Player('hamada');

    test("player has correct initial amount", function() use ($p1, $game) {
      expect($p1->getBalance())->toEqual(1500);
    });

    test("player can get pass go initially", function() use ($p1, $game) {
      $game->passGo($p1);
      expect($p1->getBalance())->toEqual(1700);
    });

    test("subsequent pass goes inc player balance", function() use($p1, $game) {
      $game->passGo($p1);
      expect($p1->getBalance())->toEqual(1900);
    });
  });

  describe("paying rents", function() use ($game) {
    $p1 = new Player('hosni');
    $p2 = new Player('ismail');
    $game->payRent($p1, $p2, new GoodDollar(26));

    test("player paying rent loses some money", function() use ($p1) {
      expect($p1->getBalance())->toEqual(1474);
    });

    test("player getting rent gains money", function() use ($p2) {
      expect($p2->getBalance())->toEqual(1526);
    });
  });
});
