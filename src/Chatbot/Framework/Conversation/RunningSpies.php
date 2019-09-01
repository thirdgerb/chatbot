<?php


namespace Commune\Chatbot\Framework\Conversation;


class RunningSpies
{
    private static $spies = [];

    public static function addSpy(string $class) : void
    {
        self::$spies[$class] = true;
    }

    /**
     * @return string[]
     */
    public static function getSpies() : array
    {
        return array_keys(self::$spies);
    }

}