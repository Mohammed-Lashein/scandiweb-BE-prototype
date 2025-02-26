<?php

class BadDollar {
  protected $amount;
  public function __construct($amount = 0) {
    $this->amount = (int)$amount;
  }
  public function getAmount() {
    return $this->amount;
  }

  public function add($dollar) {
    $this->amount += $dollar->getAmount();
  }
}

class Work {
  protected $salary;
  public function __construct() {
    $this->salary = new BadDollar(200);
  }
  public function payDay() {
    return $this->salary;
  }
}
class Person {
  public $wallet;
}

class GoodDollar {
  protected $amount;
  public function __construct($amount = 0) {
    $this->amount = (int)$amount;
  }
  public function getAmount() {
    return $this->amount;
  }

  public function add($dollar) {
    return new GoodDollar($this->amount + $dollar->getAmount());
  }
  // monopoly related methods
  /**
   * dec player savings amount
   * @param GoodDollar $amount
   */
  public function debit($amount) {
    return new GoodDollar($this->amount - $amount->getAmount());
  }
}

// Monopoly example
class Monopoly {
  protected $go_amount;
  /**
   * Game constructor
   * @return void
   */
  public function __construct() {
    $this->go_amount = new GoodDollar(200);
  }
  /**
   * Pay a player for passing 'Go'
   * @param Player $player
   * @return void
   */
  public function passGo($player) {
    $player->collect($this->go_amount);
  }

  /**
   * pay rent
   * @param Player $from player to pay
   * @param Player $to player to get money
   * @param GoodDollar $rent amount to pay
   */
  public function payRent($from, $to, $rent) {
    // $amountToReduce = - $rent->getAmount();
    // $from->collect(new GoodDollar($amountToReduce));

    // $to->collect($rent);

    $to->collect($from->pay($rent));
  }
}
class Player {
  protected $name;
  protected $savings;

  /**
   * set name and initial balance
   * @param string $name player name
   * @return void
   */
  public function __construct($name) {
    $this->name = $name;
    $this->savings = new GoodDollar(1500);
  }

  /**
   * add money to player savings
   * @param GoodDollar $amount
   * @return void
   */
  public function collect($amount) {
    $this->savings = $this->savings->add($amount);
  }

  /**
   * get player balance
   * @return int
   */
  public function getBalance() {
    return $this->savings->getAmount();
  }

  /**
   * pay rent
   * @param GoodDollar $amount amount to pay 
   */
  public function pay($amount) {
    $this->savings = $this->savings->debit($amount);
    // it is important to return the $amount so that $to->collect($from->pay($rent)) works correctly 
    /* What does it mean ?
    => So that the $amount (an instance of GoodDollar) gets passed to
    collect method 
      */
    return $amount;
  }
}