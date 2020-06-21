<?php


class Foo
{
    public $a = 3;

    public function test() : void
    {
        echo $this->a;
    }
}


class MethodTestForFoo extends ReflectionMethod
{

    public function __construct()
    {
        parent::__construct(Foo::class, 'test');
    }

    public function invoke($object, $parameter = null, $_ = null)
    {
        echo __METHOD__;
    }
}


$method = new MethodTestForFoo();
$foo = new Foo;

$method->invoke($foo);



