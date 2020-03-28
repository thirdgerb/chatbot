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
use Commune\Framework\Prototype\AApp;
use Commune\Shell\Blueprint\Kernels\RequestKernel;
use Commune\Shell\Blueprint\Render\Renderer;
use Commune\Shell\Blueprint\Shell;
use Commune\Shell\ShellConfig;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShell extends AApp implements Shell
{

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
        ChatbotConfig $config,
        ShellConfig $shellConfig,
        LogInfo $logInfo = null,
        ConsoleLogger $consoleLogger = null
    )
    {
        $reqContainer = new IShellReqContainer($procContainer);
        $debug = $config->debug;
        $this->chatbotConfig = $config;
        $this->chatbotName = $config->chatbotName;
        $this->shellConfig = $shellConfig;
        $this->shellName = $shellConfig->shellName;

        parent::__construct($procContainer, $reqContainer, $debug, $logInfo, $consoleLogger);
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