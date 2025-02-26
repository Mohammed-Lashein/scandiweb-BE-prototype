<?php

/* 
  IMPORTANT 

  The last 2 tests (of the PageDirector) are failing . 
  I have spent some time to inspect why the regex are not matched , but
  I couldn't come to a solution . 

  Since I came here to learn more about mock object, and since this
  chapter is coming to an end (finally !) , I will not continue to know
  why are the tests failing as I have learned what I came to learn .  

*/

require __DIR__ . '/../../src/LearningTests/ch6MockObj.php';
use PHPUnit\Framework\Constraint\RegularExpression;

afterEach((function() {
  /* The close() MUST be called in order for the expectations to work
  bec it cleans up the mockery container used by current test .   */
  Mockery::close();
  // echo "after each test !!";
}));


describe("Mockery docs temp example", function() {
  test("get 3 avg temps from 3 service readings ", function() {
    $this->service = Mockery::mock('service');
    $this->temperature = new Temperature($this->service);
    // readTemp should be called on the service class
    $this->service->shouldReceive('readTemp')
    // num of times the method will be called
    ->times(3)
    // what will the prev method call return consequently
    ->andReturn(10,12,14);
  
    expect($this->temperature->average())->toEqual(12);
  });
});

test("Accumulator class returns total correctly", function() {
  $acc = new Accumulator;
  calc_total([1,2,3], $acc);
  expect($acc->getTotal())->toEqual(6);
});

describe("Accumulator mock tests", function() {
  test("Calculating tax and total correctly", function(){
    $AccMock = Mockery::mock('Accumulator');
    $AccMock
    ->shouldReceive('getTotal')
    ->once()
    ->andReturn(200);
    
    // It is so weird that the IDE is throwing an err about this but
    // mockery expects Multiple Calls with Different Expectations to be handled that way
    // ->shouldReceive('add')
    // ->times(3);

  expect(calc_tax($AccMock))->toBe(14);
  // calc_total([1,2,3], $AccMock);

  /* If we didn't calculate times of method calls, we would
  have implemented the (ServerStub pattern) .   */
  });
});

test("create MockSession and test it ", function() {
  /* Chat helped me with providing the correct type annotations so that
  vs code or php inteliphense can make type hints and remove their unnecessary errors .
  
  Why do we need to precede our class with \ ?
  => To tell our IDE or php inteliphense static analyzer that Mockery 
  is present in the global namespace, not a namespace corresponding to the file we are in (which is tests) . 
  */
  /**
   * @var Session|\Mockery\MockInterface|\Mockery\LegacyMockInterface $MockSession 
   * */
  // $MockSession = Mockery::mock('Session');
  $MockSession = Mockery::spy('Session');
  $MockSession
    ->shouldReceive('isValid')
    ->andReturn(true)//;

    // $MockSession
    // ->shouldReceive('get')
    // ->andReturn(1)//;
    
    // $MockSession
    ->shouldReceive('isValid')
    ->once()
    ->with(['user_id'])
    ->andReturn("sonic");

    $MockSession
    ->shouldReceive('get')
    ->once()
    ->with(['user_id'])
    ->andReturn(true);

    $MockSession
    ->shouldNotReceive('set');

    // $MockSession->isValid('pudding');
    // $MockSession->isValid(['user_id']);
    
    expect($MockSession->isValid('pudding'))->toEqual(true);
    // expect($MockSession->get(['user_id']))->toEqual(1);

});

test("test the isValid boring stuff", function() {
  $MockSession = Mockery::mock('Session');
  $MockSession
    ->shouldReceive('isValid')
    ->once()
    ->andReturn(true)//;

    ->shouldReceive('isValid')
    ->once()
    ->with(['user_id'])
    ->andReturn("sonic")

    ->shouldNotReceive('set');

    // It seems that there is no value of calling isValid before the expect fn  
    // $MockSession->isValid(['user_id']);

    // The below code is to make sure that shouldNotReceive('set') is working
    // $MockSession->set('pud', 'ing');
    expect($MockSession->isValid('my_random_user_key'))->toEqual(true);
    expect($MockSession->isValid(['user_id']))->toEqual('sonic');
});

describe("UserLogin tests", function() {

  describe("Admin login tests", function() {
      /**
   * @var UserLogin $log
   */
  $log = new UserLogin('admin');
  test("Admin can login correctly", function() use($log){
    expect($log->getName())->toEqual('admin');
  });
  test("should login the admin on providing correct credentials", function() use($log){
    expect($log->validate('admin', 'secret'))->toBeTrue();
  });
  test("admin can't login with incorrect password", function() use($log){
    expect($log->validate('admin', 'circuit'))->toBe(false);
  });
  });

  describe('Normal User tests', function() {

    /* Very important : 
      Pest will return a notice to us if an error is triggered IN a test . 

      Since we moved the $log one level up, the triggered err is no
      longer appearing and all the tests are passing . 

      Why ?
      Because the describe block neither triggers errors nor throws exceptions . That is the responsibility of the test function . 
    */

    // NOTE : The below code is commented out bec it is
    // cluttering the terminal when I write other tests

    // $log = new UserLogin("mh"); // triggers an err which pest translates as a notice 
    // test("Normal user can't login", function() use($log){
    //   expect($log->getName())->toEqual(null);
    // });
    // test("Normal user can't be validated", function() use($log){
    //   expect($log->validate('md', 'knuckles'))->toEqual(false);
    // });

  });
});

describe("Response class tests", function() {
  $res = new Response();

  /* This regex taught me how powerful regex are . It seems that I 
  don't know regex yet ! (pun intended . If you didn't notice it, I am
  just referencing you don't know js series by kyle simpson) */
  // expect($res->fetch())->toMatch('/<form.*<input[^>]*text[^>]*name.*<input[^>]*password[^>]*passwd/ims');
  // expect($res->fetch())->toMatchConstraint(new RegularExpression('/.*<form.*<input[^>]*text[^>]*name.*<input[^>]*password[^>]*passwd/ims'));
//   expect($res->fetch())->toMatchConstraint(new RegularExpression('/.*?<form[^>]*>.*?<input[^>]*type=["\']text["\'][^>]*name=["\']name["\'].*?<input[^>]*type=["\']password["\'][^>]*name=["\']passwd["\']/ims'
// ));

});

describe("PageDirector test cases", function() {
  test("login content", function() {
    $MockSession =  Mockery::mock("Session");
    // $MockResponse = Mockery::mock("Response");
    $MockSession
      ->shouldReceive('get')
      ->with(['user_name'])
      ->once()
      ->andReturn('admin');

      $page = new PageDirector($MockSession, new Response);
      $res = $page->runPage($page);
    
      expect($res)->toMatch('/secret.*content/i');
      expect($res)->not->toMatch('/<form.*<input[^>]*text[^>]*name.*<input[^>]*password[^>]*passwd/ims');
  });
  test("logout content", function() {
    $MockSession =  Mockery::mock("Session");
    $MockSession
    ->shouldReceive('get')
    ->with(['user_name'])
    ->once()
    ->andReturn(null);

    $page = new PageDirector($MockSession, new Response);
    $res = $page->runPage($page);
  
    expect($res)->not->toMatch('/secret.*content/i');
    expect($res)->toMatchConstraint(new RegularExpression('/<form.*<input[^>]*text[^>]*name.*<input[^>]*password[^>]*passwd/ims'));

    // expect($res)->toMatch('/<form.*<input[^>]*text[^>]*name.*<input[^>]*password[^>]*passwd/ims');
  });

  // ob_start();
  // $page->run();
  /*
  ===== IMPORTANT =====  
    In the book the writer wrote $res = ob_end_clean() . 
    But this is false since ob_end_clean() returns a boolean and we want
    to access the contents of calling   $page->run();
    So the correct way is to use ob_get_contents()
   */
  // $res = ob_get_contents();
  // ob_end_clean();
});

