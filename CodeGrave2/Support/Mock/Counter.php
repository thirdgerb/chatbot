<?php

/**
 * Class Counter
 * @package Commune\Support\Mock
 */

namespace Commune\Support\Mock;


class Counter
{
    public static $count = 0;

    public $num;

    public function __construct(int $num)
    {
        $this->num = $num;
        static::$count ++;
    }

    public function getCount() : int
    {
        return static::$count;
    }

    public function plusNum() : int
    {
        $this->num ++;
        return $this->num;
    }


}