<?php

namespace Commune\Framework\Spy;

use Commune\Blueprint\CommuneEnv;

class SpyAgency
{
    /**
     * @var int[]
     */
    private static $spies = [];

    public static function incr(string $spyName) : void
    {
        if (CommuneEnv::isDebug()) {
            $count = self::$spies[$spyName] ?? 0;
            $count ++;
            self::$spies[$spyName] = $count;
        }
    }

    public static function decr(string $spyName) : void
    {
        if (CommuneEnv::isDebug()) {
            $count = self::$spies[$spyName] ?? 0;
            $count--;
            if ($count > 0) {
                self::$spies[$spyName] = $count;
            } else {
                unset(self::$spies[$spyName]);
            }
        }

    }

    /**
     * @return int[]
     */
    public static function getSpies() : array
    {
        return self::$spies;
    }


}