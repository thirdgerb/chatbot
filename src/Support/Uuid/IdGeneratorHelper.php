<?php


namespace Commune\Support\Uuid;

use Ramsey\Uuid\Uuid;

/**
 * Trait UuidImpl
 * @package Commune\Support\Uuid
 *
 */
trait IdGeneratorHelper
{
    /**
     * @var IdGenerator
     */
    protected static $idGenerator;

    public static function setGenerator(IdGenerator $generator)
    {
        static::$idGenerator = $generator;
    }

    public static function generateUuid(): string
    {
        if (isset(static::$idGenerator)) {
            return static::$idGenerator->createUuId();
        }
        return str_replace('-', '', Uuid::uuid4()->toString());
    }

    public function createUuId()
    {
        return static::generateUUid();
    }


}