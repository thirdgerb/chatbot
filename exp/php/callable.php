<?php


class A {
    public static function b($d) {
        var_dump($d);
    }
}

$c = [A::class, 'b'];
$c('f');

