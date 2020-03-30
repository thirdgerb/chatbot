<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Prototype;

use Commune\Chatbot\ChatbotConfig;
use Commune\Container\ContainerContract;
use Commune\Framework\Contracts\ConsoleLogger;
use Commune\Framework\Contracts\LogInfo;
use Commune\Framework\Prototype\AChatApp;
use Commune\Ghost\Prototype\Bootstrap\ValidateGhostContracts;
use Commune\Shell\Blueprint\Kernels\RequestKernel;
use Commune\Shell\Blueprint\Render\Renderer;
use Commune\Shell\Blueprint\Shell;
use Commune\Shell\Prototype\Bootstrap;
use Commune\Shell\ShellConfig;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShell extends AChatApp implements Shell
{
    protected $bootstrappers = [
        Bootstrap\RegisterShellProviders::class,
        ValidateGhostContracts::class,
    ];

    /**
     * @var ChatbotConfig
     */
    protected $chatbotConfig;

    /**
     * @var string
     */
    protected $chatbotName;

    /**
     * @var string
     */
    protected $shellName;

    /**
     * @var ShellConfig
     */
    protected $shellConfig;

    public function __construct(
        ContainerContract $procContainer,
        ChatbotConfig $chatbotConfig,
        ShellConfig $shellConfig,
        LogInfo $logInfo = null,
        ConsoleLogger $consoleLogger = null
    )
    {
        $reqContainer = new IShellReqContainer($procContainer);
        $debug = $chatbotConfig->debug;
        $this->chatbotConfig = $chatbotConfig;
        $this->chatbotName = $chatbotConfig->chatbotName;
        $this->shellConfig = $shellConfig;
        $this->shellName = $shellConfig->shellName;

        parent::__construct($procContainer, $reqContainer, $debug, $logInfo, $consoleLogger);
    }

    protected function basicBinding(): void
    {
        parent::basicBinding();

        // 绑定配置
        $this->procContainer->instance(ChatbotConfig::class, $this->chatbotConfig);
        $this->reqContainer->instance(ChatbotConfig::class, $this->chatbotConfig);

        $this->procContainer->instance(ShellConfig::class, $this->shellConfig);
        $this->reqContainer->instance(ShellConfig::class, $this->shellConfig);

        // 绑定 Shell 与 App
        $this->procContainer->instance(Shell::class, $this);
        $this->reqContainer->instance(Shell::class, $this);

        // 绑定 Kernel
        $this->procContainer->bind(RequestKernel::class, $this->shellConfig->requestKernel);

    }

    /**
     * 生成一个响应请求的内核, 理论上不应该是单例.
     *
     * @return RequestKernel
     */
    public function getReqKernel(): RequestKernel
    {
        return $this->getProcContainer()->get(RequestKernel::class);
    }

    public function getChatbotName(): string
    {
        return $this->chatbotName;
    }

    public function getShellName(): string
    {
        return $this->shellName;
    }

    public function getRenderer(): Renderer
    {
        return $this->getProcContainer()->get(Renderer::class);
    }


}