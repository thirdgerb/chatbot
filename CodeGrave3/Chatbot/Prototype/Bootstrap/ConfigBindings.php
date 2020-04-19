<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Prototype\Bootstrap;

use Commune\Chatbot\Blueprint\Chatbot;
use Commune\Chatbot\ChatbotConfig;
use Commune\Framework\Contracts\Bootstrapper;
use Commune\Framework\Contracts\ConsoleLogger;
use Commune\Framework\Contracts\LogInfo;
use Commune\Framework\Exceptions\BootingException;
use Commune\Support\Struct\Struct;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ConfigBindings implements Bootstrapper
{
    /**
     * @var ChatbotConfig
     */
    protected $config;

    /**
     * @var Chatbot
     */
    protected $chatbot;

    /**
     * @var ConsoleLogger
     */
    protected $console;

    /**
     * @var LogInfo
     */
    protected $logInfo;

    /**
     * ConfigBindings constructor.
     * @param ChatbotConfig $config
     * @param Chatbot $chatbot
     * @param ConsoleLogger $console
     * @param LogInfo $logInfo
     */
    public function __construct(ChatbotConfig $config, Chatbot $chatbot, ConsoleLogger $console, LogInfo $logInfo)
    {
        $this->config = $config;
        $this->chatbot = $chatbot;
        $this->console = $console;
        $this->logInfo = $logInfo;
    }


    public function bootstrap(): void
    {
        $container = $this->chatbot->getProcContainer();
        foreach ($this->config->configs as $index => $value) {

            if (
                is_string($index)
                && is_a($index, Struct::class, TRUE)
                && is_array($value)
            ) {

                $container->instance($index, new $index($value));
            } elseif(
                is_string($value)
                && is_a($value, Struct::class, TRUE)
            ) {
                $container->instance($value, new $value);
            }

            throw new BootingException(

            );
        }
    }


}