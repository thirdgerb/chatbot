<?php

namespace Commune\Support\RunningSpy;


class SpyAgency
{
    private static $spies = [];

    /**
     * 可决定是否使用.
     * @var bool
     */
    public static $running = false;

    public static function addSpy(string $class) : void
    {
        self::$spies[$class] = true;
    }

    /**
     * 检查 running spy 功能是否启用.
     * @return bool
     */
    public static function isRunning() : bool
    {
        return self::$running;
    }

    /**
     * @return string[]
     */
    public static function getSpies() : array
    {
        return array_keys(self::$spies);
    }


}