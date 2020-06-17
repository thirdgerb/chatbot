<?php


abstract class Test {

}

interface Test2 {
}

$r = new ReflectionClass(Test::class);

var_dump($r->isAbstract(), $r->isInstantiable(), $r->isInterface());

$r = new ReflectionClass(Test2::class);
var_dump($r->isAbstract(), $r->isInstantiable(), $r->isInterface());
