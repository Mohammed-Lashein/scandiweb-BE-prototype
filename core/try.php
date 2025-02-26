<?php

$myArr = [1,2,3];
$my_2nd_arr = $myArr;
array_push($my_2nd_arr, 'aloha');

var_dump($myArr);
var_dump($my_2nd_arr);