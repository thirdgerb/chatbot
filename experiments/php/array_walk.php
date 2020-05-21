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