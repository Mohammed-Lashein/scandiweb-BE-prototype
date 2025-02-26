<?php 

$y = 'hi';
function aloha(){
  $x = 1;
  global $y;
  echo $y;
}
if(true) {
  $z = 'heneez';
}
aloha();
var_dump($x);
var_dump($z);