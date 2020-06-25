<?php


$a = [
    'b' => [
        'c' => [
            'd'
        ],
        'e'
    ],
    'f'
];


array_walk($a, function($val, $key) {
    var_dump(func_get_args());
    return '123';
});

var_dump($a);

array_walk_recursive($a, function() {
    var_dump(func_get_args());
});


$arr = ['a' => 1, 'b' => 2, 'c' => 3];
array_walk($arr, function($val, $key) {
    $key .= 't';
    $val++;
});

var_dump($arr);

array_walk($arr, function($reference, &$key) {
    $key .= 't';
    $reference++;
});

var_dump($arr);
