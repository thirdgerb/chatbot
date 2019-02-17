<?php

/**
 * Class IntentFactory
 * @package Commune\Chatbot\Framework\Intent
 */

namespace Commune\Chatbot\Framework\Intent;


use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Intent\Matchers\RegexMatcher;
use Commune\Chatbot\Framework\Intent\Matchers\TypeMatcher;
use Commune\Chatbot\Framework\Message\Message;
use Commune\Chatbot\Framework\Routing\IntentRoute;
use Commune\Chatbot\Framework\Routing\Router;
use Illuminate\Console\Parser;
use Commune\Chatbot\Framework\Intent\Matchers\Matcher;

class IntentFactory
{

    /**
     * @var string
     */

    protected $id;
    /**
     * @var string | null
     */
    protected $intentName;

    /**
     * @var string
     */
    protected $commandName = '';

    /**
     * 为null 不对入参做校验
     * @var IntentDefinition | null
     */
    protected $definition;

    /**
     * @var IntentCfg | null
     */
    protected $intentCfg;

    protected $exactly = [];

    protected $matchers = [];

    protected $messageType;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getCommandName(): string
    {
        return $this->commandName;
    }

    public function setIntentCfg(IntentCfg $intentCfg)
    {
        $this->intentCfg = $intentCfg;
        $this->intentName = $intentCfg->getIntentName();
        $this->setCommand($intentCfg->getSignature());
    }

    public function setCommand(string $signature)
    {
        list($name, $arguments, $options) = Parser::parse($signature);
        $this->commandName = $name;
        $this->definition = new IntentDefinition();
        $arguments = $arguments ?? [];
        $options = $options ?? [];
        $this->definition->addArguments($arguments);
        $this->definition->addOptions($options);
    }

    public function addExactly(string $exactly)
    {
        $this->exactly[] = $exactly;
    }

    public function addRegex(array $defined)
    {
        $this->matchers[] = new RegexMatcher($this, $defined);
    }

    public function addMatcher(Matcher $matcher)
    {
        $this->matchers[] = $matcher;
    }

    /**
     * @param string $messageType
     * @throws \ReflectionException
     */
    public function addMessageType(string $messageType)
    {
        $messageType = trim($messageType, '\\');
        if (!class_exists($messageType)) {
            //todo
            throw new ConfigureException();
        }

        $r = new \ReflectionClass($messageType);
        if (!$r->isSubclassOf(Message::class)) {
            //todo
            throw new ConfigureException();
        }

        $this->matchers[] = new TypeMatcher($messageType);
    }


    public function match(Conversation $conversation) : ? Intent
    {
        if ($conversation->getMessageType() === $this->messageType) {
            return $this->makeIntent($conversation->defaultIntent());
        }

        // 如果有精确匹配
        if (in_array($conversation->getTrimText(), $this->exactly)) {
            return $this->makeIntent($conversation->defaultIntent());
        }

        // 如果有命令
        $intent = $conversation->getCommandIntent();
        if (isset($intent) && isset($this->definition)) {
            $intentCommandName = $intent->getId();
            if ( $intentCommandName === $this->commandName) {
                return $this->makeIntent($intent);

            // 如果是命令, 则不能走其它的匹配了.
            } else {
                return null;
            }
        }


        //正则 与其它 规范检查
        if (!empty($this->matchers)) {
            foreach($this->matchers as $matcher) {
                /**
                 * @var Matcher $matcher
                 * @var Intent|null $intent
                 */
                $intent = $matcher->match($conversation);
                if (isset($intent)) {
                    return $this->makeIntent($intent);
                }
            }
        }

        return null;
    }

    /**
     * 如果 定义了 intentCfg, intentCfg 可以预定义处理逻辑, 无视Context
     *
     * @param ChatbotApp $app
     * @param Router $router
     * @return IntentRoute|null
     */
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

    protected function makeIntent(Intent $intent) : Intent
    {
        if (isset($this->definition)) {
            $intent->bind($this->definition);
        }
        return $intent;
    }

}