<?php


namespace Commune\Support\Uuid;


interface HasIdGenerator
{

    public static function setGenerator(IdGenerator $generator);

    public static function generateUuid() : string;

}