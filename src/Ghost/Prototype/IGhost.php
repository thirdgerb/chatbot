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
use Commune\Framework\Prototype\AApp;
use Commune\Ghost\Blueprint\Ghost;
use Commune\Ghost\Blueprint\Kernels;
use Commune\Chatbot\ChatbotConfig;
use Commune\Ghost\GhostConfig;
use Commune\Ghost\Prototype\Bootstrap;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
class IGhost extends AApp implements Ghost
{
    /*------- config -------*/

    protected $bootstrappers = [
        Bootstrap\BootGhost::class,
        Bootstrap\RegisterProviders::class,
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

    public function __construct(
        ContainerContract $procContainer,
        ChatbotConfig $config,
        LogInfo $logInfo = null,
        ConsoleLogger $consoleLogger = null
    )
    {
        $reqContainer = new IGhostReqContainer($procContainer);
        $debug = $config->debug;
        $this->chatbotConfig = $config;
        $this->chatbotName = $config->chatbotName;
        parent::__construct($procContainer, $reqContainer, $debug, $logInfo, $consoleLogger);
    }

    public function getChatbotName(): string
    {
        return $this->chatbotName;
    }


    protected function basicBinding() : void
    {
        // 绑定配置
        $this->procContainer->instance(ChatbotConfig::class, $this->chatbotConfig);
        $this->reqContainer->instance(ChatbotConfig::class, $this->chatbotConfig);

        $config = $this->chatbotConfig->ghost;
        $this->procContainer->instance(GhostConfig::class, $config);
        $this->reqContainer->instance(GhostConfig::class, $config);

        // 绑定 Ghost
        $this->procContainer->instance(Ghost::class, $this);
        $this->reqContainer->instance(Ghost::class, $this);

        // 绑定 Kernel
        $this->procContainer->bind(Kernels\MessageKernel::class, $config->messageKernel);
        $this->procContainer->bind(Kernels\ApiKernel::class, $config->apiKernel);
        $this->procContainer->bind(Kernels\AsyncKernel::class, $config->asyncKernel);
    }


    public function getApiKernel(): Kernels\ApiKernel
    {
        return $this->getProcContainer()->get(Kernels\ApiKernel::class);
    }

    public function getAsyncKernel(): Kernels\AsyncKernel
    {
        return $this->getProcContainer()->get(Kernels\AsyncKernel::class);
    }

    public function getMessageKernel(): Kernels\MessageKernel
    {
        return $this->getProcContainer()->get(Kernels\MessageKernel::class);
    }


}