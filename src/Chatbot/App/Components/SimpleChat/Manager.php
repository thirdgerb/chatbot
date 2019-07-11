<?php


namespace Commune\Chatbot\App\Components\SimpleChat;


use Commune\Chatbot\Framework\Exceptions\ConfigureException;

class Manager
{

    /**
     * @var string[]
     */
    protected static $resources = [];

    /**
     * @var string[][][]
     */
    protected static $loaded = [];

    public static function loadResource(string $index, string $resource) : void
    {
        if (isset(static::$resources[$index])) {
            return;
        }

        if (!file_exists($resource)) {
            throw new ConfigureException(
                __METHOD__
                . ' resource json file ' . $resource
                . ' not exists'
            );
        }

        $content = file_get_contents($resource);
        $data = json_decode($content, true);

        if (empty($data)) {
            throw new ConfigureException(
                __METHOD__
                . ' resource json file ' . $resource
                . ' is invalid json'
            );
        }

        foreach ($data as $intentName => $replies) {
            if (!is_string($intentName) || !is_array($replies)) {
                throw new  ConfigureException(
                    __METHOD__
                    . ' resource json file ' . $resource
                    . ' is invalid, only intentName => string[] accept'
                );
            }
            static::setIntentReplies($index, $intentName, $replies);
        }

        static::$resources[$index] = $resource;
    }

    public static function saveResource(string $index) : void
    {
        if (!isset(static::$resources[$index])) {
            throw new \InvalidArgumentException(
                __METHOD__
                . ' resource ' . $index
                . ' not preload'
            );
        }
        
        $path = static::$resources[$index];
        $data = static::$loaded[$index];

        file_put_contents($path, json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ));
    }

    /**
     * @param string $index
     * @param string $intentName
     * @param string[] $replies
     */
    public static function setIntentReplies(string $index, string $intentName, array $replies)
    {
        static::$loaded[$index][$intentName] = [];
        foreach ($replies as $text) {
            static::$loaded[$index][$intentName][] = (string) $text;
        }
    }

    /**
     * @param string $index
     * @return bool
     */
    public static function hasPreload(string $index) : bool
    {
        return isset(static::$loaded[$index]);
    }

    public static function listResources() : array
    {
        return array_map(function(string $path){
            $sections = explode('/', $path);
            $count = count($sections);
            $left = $count > 4 ? $count - 4 : $count - 1;

            for ($i =0; $i< $left ; $i ++) {
                array_shift($sections);
            }

            return '../'. implode('/', $sections);

        }, static::$resources);
    }

    public static function match(string $index, string $intentName) : ? string
    {
        $replies = static::matchReplies($index, $intentName);

        if (empty($replies)) {
            return null;
        }

        $count = count($replies);
        if ($count === 1) {
            return $replies[0];
        }

        $choose = rand(0, $count - 1);
        return $replies[$choose];
    }

    public static function listResourceIntents(string $index) : array
    {
        if (static::hasPreload($index)) {
            return array_keys(static::$loaded[$index]);
        }

        return [];
    }

    public static function matchReplies(string $index, string $intentName) :  array
    {
        if (!static::hasPreload($index)) {
            return null;
        }

        return static::$loaded[$index][$intentName] ?? [];
    }
}