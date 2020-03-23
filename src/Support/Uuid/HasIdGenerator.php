<?php


namespace Commune\Support\Uuid;


/**
 * 用于标记一个类可以生成 UUID
 * 可以通过 setGenerator 的方式修改 generator
 */
interface HasIdGenerator
{

    public static function setGenerator(IdGenerator $generator);

    public static function generateUuid() : string;

}