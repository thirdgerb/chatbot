<?php


class ObjectVarTest {

    public $a = 1;

    protected $b = 1;

    private $c = 1;

}

$o = new ObjectVarTest();

var_dump(get_object_vars($o));
