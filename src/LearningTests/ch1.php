<?php

define('TAX_RATE', 0.07);
function calculate_sales_tax($amount) {
 return (int) round($amount * TAX_RATE); 
}

// classes definitions
class CartLine {
  public $price = 0;
  public $qty = 0;
  public function calculateCartLineTotal($line) {
    return  $line->price * $line->qty;
  }

}
class Cart {
  private $lines = [];
  public function addLine($line) {
    $this->lines[] = $line;
  }
  protected function addTaxesToTotal($total) {
    return $total * 1.07;
  }
  public function calcTotal() {
    $total = 0;
    foreach($this->lines as $line) {
      $total += $line->calculateCartLineTotal($line);
    }
    $total = $this->addTaxesToTotal($total);
    return $total;
  }
}

