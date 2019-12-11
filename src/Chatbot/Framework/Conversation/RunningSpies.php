<?php


namespace Commune\Chatbot\Framework\Conversation;


class RunningSpies
{
    private static $spies = [];

    /**
     * 可决定是否使用.
     * @var null|bool
     */
    public static $run = null;

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
        return self::$run ?? (defined('CHATBOT_DEBUG') ? CHATBOT_DEBUG : false);
    }

    /**
     * @return string[]
     */
    public static function getSpies() : array
    {
        return array_keys(self::$spies);
    }


}