<?php

/**
 * Class PropertyReader
 * @package Commune\Support
 */

namespace Commune\Support\Property;


use Commune\Support\Arr\ArrayAndJsonAble;

interface PropertyReaderInterface extends ArrayAndJsonAble
{

    /**
     * 获取数据源的类别.
     * @return string
     */
    public function getOriginType(): string;

    /**
     * 获取原始数据源.
     * @return mixed
     */
    public function getOriginData();

    /**
     * 获取某个属性的方法.
     * @param string $name
     * @return mixed
     */
    public function getProperty($name);


    /**
     * 为某一个数据源注册一个 parser
     * @param string $originType
     * @param callable $parser
     */
    public static function register(string $originType, callable $parser) : void;

    /**
     * 注册一个parser
     * @param PropertyReaderParser $parser
     */
    public static function registerParser(PropertyReaderParser $parser) : void;

}