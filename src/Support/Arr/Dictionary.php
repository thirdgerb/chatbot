<?php


namespace Commune\Support\Arr;


interface Dictionary extends \ArrayAccess
{
    public function __get(string $name);

    public function __set(string $name, $value) : void;

    public function __isset(string $name) : bool;

    public function __unset(string $name) : void;

}