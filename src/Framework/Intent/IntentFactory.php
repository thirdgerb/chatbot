<?php

/**
 * Class IntentMatcher
 * @package Commune\Chatbot\Framework\Intent
 */

namespace Commune\Chatbot\Framework\Intent;


use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Intent\Matcher\CommandMatcher;
use Commune\Chatbot\Framework\Intent\Matcher\RegexMatcher;
use Commune\Chatbot\Framework\Routing\IntentRoute;
use Commune\Chatbot\Framework\Routing\Router;
use Commune\Chatbot\Framework\Intent\Matcher\Matcher;
use Commune\Chatbot\Framework\Message\Message;

class IntentFactory
{

    /**
     * @var string | null
     */
    protected $intentName;

    protected $matchers = [];

    protected $defaultEntities = [];
    /**
     * @var IntentCfg
     */
    protected $intentCfg;

    public function __construct(string $intentCfgName = null)
    {
        $this->intentName = $intentCfgName ?? '';
        if ($intentCfgName) {
            $this->intentCfg = new $intentCfgName;
        }
    }

    public static function makeByIntent(string $intentCfgName) : IntentFactory
    {
        if (!class_exists($intentCfgName)) {
            //todo
            throw new ConfigureException();
        }

        try {
            $ref = new \ReflectionClass($intentCfgName);
            if (!$ref->isSubclassOf(IntentCfg::class)) {
                //todo
                throw new ConfigureException();
            }

        } catch (\ReflectionException $e) {
            //todo
            throw new ConfigureException('', 0, $e);
        }

        $factory = new static($intentCfgName);

        $signature = constant("$intentCfgName::SIGNATURE");
        if (!empty($signature)) {
            $factory->addCommand($signature);
        }

        $regex = constant("$intentCfgName::REGEX");
        if (!empty($regex)) {
            $factory->addRegex($regex);
        }
        $factory->defaultEntities = constant("$intentCfgName::ENTITIES");

        return $factory;
    }

    public function addCommand(string $signature)
    {
        $this->matchers[] = new CommandMatcher($signature);
    }

    public function addRegex(array $defined)
    {
        $pattern = array_shift($defined);
        $this->matchers[] = new RegexMatcher($pattern, $defined);
    }

    public function addMatcher(callable $matcher)
    {
        $this->matchers[] = $matcher;
    }

    public function defaultRoute(ChatbotApp $app, Router $router) : ? IntentRoute
    {
        return isset($this->intentCfg)
            ? $this->intentCfg->defaultRoute($app, $router)
            : null;
    }

    /**
     * @return string
     */
    public function getIntentName(): string
    {
        return $this->intentName;
    }


    public function match(Message $message) : ? Intent
    {
        $entities = null;

        if (!empty($this->matchers)) {
            foreach($this->matchers as $matcher) {
                /**
                 * @var Matcher $matcher
                 */
                $entities = call_user_func($matcher, $message);
                if (isset($entities)) {
                    return $this->wrapIntent($entities, $message);
                }
            }
        }

        return null;
    }

    public function defaultIntent(Message $message) : Intent
    {
        return $this->wrapIntent($this->defaultEntities, $message);
    }

    protected function wrapIntent(array $entities, $message)
    {
        return new Intent($message, $entities, $this->intentName);
    }
}