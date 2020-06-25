<?php

require __DIR__ . '/../../vendor/autoload.php';

echo $str =
    'test/a/b/c'
    . '?'
    . http_build_query(['a' => 1, 'b' => '中文', 'c' => 3, 'd' => [1,2,3]])
    . '#'
    . 'tag';




$parsed = parse_url($str);
var_dump($parsed);

$query = $parsed['query'] ?? '';

$queryArr = query($query, 1);
var_dump($queryArr);
