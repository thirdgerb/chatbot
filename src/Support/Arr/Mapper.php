<?php

/**
 * Class Mapper
 * @package Commune\Support
 */

namespace Commune\Support\Arr;


/**
 * 链式构建数组的简单工具
 * 主要目的是用注释增加规范性.
 *
 * Class Mapper
 * @package Commune\Support
 */
class Mapper implements ArrayAndJsonAble
{
    use ArrayAbleToJson;

    protected $data = [];

    /**
     * Mapper constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $name
     * @param $value
     * @return static
     */
    public function with(string $name, $value) : Mapper
    {
        $this->data[$name] = $value;
        return $this;
    }

    public function toArray() : array
    {
        return $this->data;
    }

    public function __call($name, $arguments)
    {
        return $this->with($name, $arguments[0]);
    }

    /**
     * @param array $data
     * @return static
     */
    public static function make($data = []) : Mapper
    {
        return new static($data);
    }

    public static function __callStatic($name, $arguments)
    {
        return new static([
            $name => $arguments[0]
        ]);
    }

}