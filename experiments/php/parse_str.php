<?php

require_once __DIR__ .'/../../vendor/autoload.php';

$arr = [
    'a' => 123,
    'b' => 1,
    4 => 'abc',
    'c' => [3,4],
    'd' => [
        1 => 5,
        'k' => '6'
    ]
];

$query = http_build_query($arr);
echo "\nquery:=";
var_dump($query);


$arr2 = [];
parse_str($query,$arr2);
echo "\nparse query:=";
var_dump($arr2);
echo "\nquery equals=";
var_dump($arr == $arr2);


$arr3 = [];
parse_str('sjdflajweow', $arr3);
var_dump($arr3);
