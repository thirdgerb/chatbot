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
use Commune\Framework\Contracts\ServiceProvider;
use Commune\Framework\Exceptions\BootingException;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RegisterChatbotProviders implements Bootstrapper
{
    /**
     * @var Chatbot
     */
    protected $chatbot;

    /**
     * @var ChatbotConfig
     */
    protected $chatbotConfig;

    /**
     * @var LogInfo
     */
    protected $logInfo;

    /**
     * @var ConsoleLogger
     */
    protected $console;

    /**
     * RegisterProviders constructor.
     * @param Chatbot $chatbot
     * @param ChatbotConfig $chatbotConfig
     * @param LogInfo $logInfo
     * @param ConsoleLogger $console
     */
    public function __construct(Chatbot $chatbot, ChatbotConfig $chatbotConfig, LogInfo $logInfo, ConsoleLogger $console)
    {
        $this->chatbot = $chatbot;
        $this->chatbotConfig = $chatbotConfig;
        $this->logInfo = $logInfo;
        $this->console = $console;
    }


    public function bootstrap(): void
    {
        /**
         * @var ServiceProvider[] $providers
         */
        $providers = [];
        foreach ($this->chatbotConfig->providers as $index => $value) {

            $providerName = '';

            if (
                is_string($index)
                && is_a($index, ServiceProvider::class, TRUE)
                && is_array($value)
            ) {
                $providerName = $index;
                /**
                 * @var ServiceProvider $provider
                 */
                $provider = new $providerName($value);
                $providers[$provider->getId()] = $provider;

            } elseif(is_string($value) && is_a($value, ServiceProvider::class, TRUE)) {

                $providerName = $value;
                /**
                 * @var ServiceProvider $provider
                 */
                $provider = new $providerName;
                $providers[$provider->getId()] = $provider;

            } else {
                throw new BootingException(
                    $this->logInfo->bootInvalidProviderConfiguration($index, $value)
                );
            }

        }

        $container = $this->chatbot->getProcContainer();


        foreach ($providers as $id => $provider) {
            $provider->register($container);

            $this->console->debug(
                $this->logInfo->bootRegisterProvider($id)
            );
        }

        foreach ($providers as $provider) {
            $provider->boot($container);
        }
    }


}