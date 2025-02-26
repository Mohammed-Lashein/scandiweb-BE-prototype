<?php

require __DIR__ . '/../../src/LearningTests/ch1.php';

/* A good note to know : From Pest docs, the test file name should be 
suffixed with Test otherwise, pest won't recognize it as a test file 
and will be simply ignored . 
I used to write it Ch1Test(s) which what made the tests not work . 
*/

it("calculates taxes correctly", function() {
  expect(calculate_sales_tax(100))->toBe(7);
});

it('calculates cart total', function() {
  $line1 = new CartLine();
  $line1->price = 12;
  $line1->qty = 2;

  $cart = new Cart();
  $cart->addLine($line1);

  expect($cart->calcTotal())->toEqual(12*2*1.07);
});