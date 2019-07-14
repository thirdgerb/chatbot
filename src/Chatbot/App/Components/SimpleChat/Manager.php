<?php


namespace Commune\Chatbot\App\Components\SimpleChat;


use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\Context\Intent\PlaceHolderIntentDef;
use Commune\Chatbot\OOHost\NLU\NLUExample;
use Symfony\Component\Yaml\Yaml;

class Manager
{

    /**
     * chat id => 文件地址. 用来保存的时候可以操作.
     * @var string[]
     */
    protected static $resources = [];

    /**
     * 'id' => [
     *    'intentName' => [
     *       'reply1',
     *       'reply2',
     *    ]
     * ]
     * @var string[][][]
     */
    protected static $replies = [];

    /**
     * 'id' => [
     *    'intentName' => [
     *       'example1',
     *       'example2',
     *    ]
     * ]
     * @var string[][][]
     */
    protected static $examples = [];

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
        try {
            $data = Yaml::parse($content);

        } catch (\Exception $e) {
            throw new ConfigureException(
                __METHOD__
                . "parse yaml file $resource failed",
                $e
            );
        }

        if (empty($data)) {
            throw new ConfigureException(
                __METHOD__
                . ' resource yaml file ' . $resource
                . ' is invalid'
            );
        }

        foreach ($data as $intentName => $option) {
            if (!is_string($intentName) || !is_array($option)) {
                throw new  ConfigureException(
                    __METHOD__
                    . ' resource json file ' . $resource
                    . ' is invalid, only intentName => string[] accept'
                );
            }

            $examples = $option['examples'] ?? [];
            $replies = $option['replies'] ?? [];

            static::setIntentExamples($index, $intentName, $examples);
            static::setIntentReplies($index, $intentName, $replies);
        }

        static::$resources[$index] = $resource;
    }

    /**
     * @param string $index
     * @param string $intentName
     * @param string[] $examples
     */
    public static function setIntentExamples(
        string $index,
        string $intentName,
        array $examples
    ) : void
    {
        if (empty($examples)) {
            return;
        }

        static::$examples[$index][$intentName] = [];

        foreach ($examples as $text) {
            static::$examples[$index][$intentName][] = (string) $text;
        }

        $repo = IntentRegistrar::getIns();

        if (!$repo->has($intentName)) {
            $repo->register(new PlaceHolderIntentDef($intentName));
        }

        foreach ($examples as $example) {
            $repo->registerNLUExample($intentName, new NLUExample($example));
        }
    }

    /**
     * @param string $index
     */
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
        $data = [];

        $replies = static::$replies;
        ksort($replies);
        foreach ($replies as $id => $intentReplies) {
            foreach ($intentReplies as $intentName => $replies) {
                if (!empty($replies)) {
                    $data[$intentName]['replies'] = array_values($replies);
                }
            }
        }

        foreach (static::$examples as $id => $intentExamples) {
            foreach ($intentExamples as $intentName => $examples) {
                if (!empty($examples)) {
                    $data[$intentName]['examples'] = array_values($examples);
                }
            }
        }


        $content = Yaml::dump($data, 4, 2);
        file_put_contents($path, $content);
    }

    /**
     * @param string $index
     * @param string $intentName
     * @param string[] $replies
     */
    public static function setIntentReplies(string $index, string $intentName, array $replies)
    {
        if (empty($replies)) {
            return;
        }
        static::$replies[$index][$intentName] = [];
        foreach ($replies as $text) {
            static::$replies[$index][$intentName][] = (string) $text;
        }
    }

    /**
     * @param string $index
     * @return bool
     */
    public static function hasPreload(string $index) : bool
    {
        return isset(static::$resources[$index]);
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
            return array_keys(static::$replies[$index]);
        }

        return [];
    }

    public static function matchReplies(string $index, string $intentName) :  array
    {
        if (!static::hasPreload($index)) {
            return [];
        }

        return static::$replies[$index][$intentName] ?? [];
    }
}