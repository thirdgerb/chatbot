<?php


class A {
    public static function on()
    {
        var_dump(__FUNCTION__);
    }
}


var_dump(method_exists(A::class, 'on'));

var_dump(function_exists('A::on'));

var_dump(is_callable('A::on'));

var_dump(is_callable([A::class, 'on']));

