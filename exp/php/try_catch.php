<?php

function test(bool $throw) {

    try {

        $a = 1;

        if ($throw) {
            throw new Exception();
        }

    } catch (\Throwable $e) {
        $a = 2;

    } finally {

        return $a;
    }

}


var_dump(test(true));
var_dump(test(true));
