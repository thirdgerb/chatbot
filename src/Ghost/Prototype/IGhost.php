<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype;

use Commune\Container\ContainerContract;
use Commune\Framework\Contracts\ConsoleLogger;
use Commune\Framework\Contracts\LogInfo;
use Commune\Framework\Prototype\AChatApp;
use Commune\Ghost\Blueprint\Ghost;
use Commune\Ghost\Blueprint\GhostKernel;
use Commune\Chatbot\ChatbotConfig;
use Commune\Ghost\GhostConfig;
use Commune\Ghost\Prototype\Bootstrap;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
class IGhost extends AChatApp implements Ghost
{
    /*------- config -------*/

    protected $bootstrappers = [
        Bootstrap\RegisterGhostProviders::class,
        Bootstrap\ValidateGhostContracts::class,
    ];


    /*------- cached -------*/

    /**
     * @var ChatbotConfig
     */
    protected $chatbotConfig;

    /**
     * @var string
     */
    protected $chatbotName;

    /**
     * @var GhostConfig
     */
    protected $ghostConfig;

    public function __construct(
        ContainerContract $procContainer,
        ChatbotConfig $chatbotConfig,
        GhostConfig $ghostConfig,
        LogInfo $logInfo = null,
        ConsoleLogger $consoleLogger = null
    )
    {
        $reqContainer = new IGhostReqContainer($procContainer);
        $debug = $chatbotConfig->debug;
        $this->chatbotConfig = $chatbotConfig;
        $this->ghostConfig = $ghostConfig;
        $this->chatbotName = $chatbotConfig->chatbotName;
        parent::__construct($procContainer, $reqContainer, $debug, $logInfo, $consoleLogger);
    }

    public function getChatbotName(): string
    {
        return $this->chatbotName;
    }


    protected function basicBinding() : void
    {
        parent::basicBinding();

        // 绑定配置
        $this->procContainer->instance(ChatbotConfig::class, $this->chatbotConfig);
        $this->reqContainer->instance(ChatbotConfig::class, $this->chatbotConfig);

        $this->procContainer->instance(GhostConfig::class, $this->ghostConfig);
        $this->reqContainer->instance(GhostConfig::class, $this->ghostConfig);

        // 绑定 Ghost 与 App
        $this->procContainer->instance(Ghost::class, $this);
        $this->reqContainer->instance(Ghost::class, $this);

        // 绑定 Kernel
        $this->procContainer->bind(GhostKernel::class, $this->ghostConfig->kernel);
    }

    public function getKernel(): GhostKernel
    {
        return $this->procContainer->make(GhostKernel::class);
    }


}