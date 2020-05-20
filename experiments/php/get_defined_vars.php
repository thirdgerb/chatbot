<?php


function test($a = 1, $b = 2) {
    $result = get_defined_vars();
    $c = 3;
    var_dump($result);
}


$d = 4;
test();
