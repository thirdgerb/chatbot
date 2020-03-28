<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot;

use Commune\Chatbot\Blueprint\Chatbot;
use Commune\Container\ContainerContract;
use Commune\Container\IlluminateAdapter;
use Commune\Framework\Contracts\Bootstrapper;
use Commune\Framework\Contracts\ConsoleLogger;
use Commune\Framework\Contracts\LogInfo;
use Commune\Framework\Prototype\Log\IConsoleLogger;
use Commune\Framework\Prototype\Log\ILogInfo;
use Commune\Ghost\Blueprint\Ghost;
use Commune\Ghost\Prototype\IGhost;
use Commune\Shell\Blueprint\Shell;
use Commune\Shell\Prototype\IShell;
use Illuminate\Container\Container;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IChatbot implements Chatbot
{

    /*----- bootstrapper -----*/

    protected $bootstrappers = [

    ];

    /*----- cached -----*/

    /**
     * @var ChatbotConfig
     */
    protected $chatbotConfig;

    /**
     * @var ContainerContract
     */
    protected $container;

    /**
     * @var LogInfo
     */
    protected $logInfo;

    /**
     * @var ConsoleLogger
     */
    protected $consoleLogger;

    /**
     * @var string
     */
    protected $chatbotName;

    /**
     * @var bool
     */
    protected $booted = false;

    /**
     * IChatbot constructor.
     * @param ChatbotConfig $chatbotConfig
     * @param ContainerContract|null $container
     * @param LogInfo|null $logInfo
     * @param ConsoleLogger|null $consoleLogger
     */
    public function __construct(
        ChatbotConfig $chatbotConfig,
        ContainerContract $container = null,
        LogInfo $logInfo = null,
        ConsoleLogger $consoleLogger = null
    )
    {
        $this->chatbotConfig = $chatbotConfig;
        $this->container = $container ?? new IlluminateAdapter(new Container());
        $this->logInfo = $logInfo ?? new ILogInfo();
        $this->consoleLogger = $consoleLogger ?? new IConsoleLogger($this->chatbotConfig->debug);
        $this->basicBinding();
    }


    protected function basicBinding() : void
    {
        // 绑定日志
        $this->container->instance(LogInfo::class, $this->logInfo);
        $this->container->instance(ConsoleLogger::class, $this->consoleLogger);
        $this->container->instance(ChatbotConfig::class, $this->chatbotConfig);

        // 绑定自己
        $this->container->instance(Chatbot::class, $this);
    }

    public function getChatbotName(): string
    {
        return $this->chatbotName;
    }

    public function getProcContainer(): ContainerContract
    {
        return $this->container;
    }


    public function bootstrap(): void
    {
        if ($this->booted) {
            return;
        }

        $this->consoleLogger->info(
            $this->logInfo->bootStartKeyStep(__METHOD__)
        );

        foreach ($this->bootstrappers as $bootstrapperName) {

            $this->consoleLogger->info(
                $this->logInfo->bootStartKeyStep($bootstrapperName)
            );

            /**
             * @var Bootstrapper $bootstrapper
             */
            $bootstrapper = $this->getProcContainer()->get($bootstrapperName);
            $bootstrapper->bootstrap();

            $this->consoleLogger->info(
                $this->logInfo->bootEndKeyStep($bootstrapperName)
            );
        }

        $this->consoleLogger->info(
            $this->logInfo->bootEndKeyStep(__METHOD__)
        );
        $this->booted = true;
    }

    public function getGhost(): Ghost
    {
        $this->bootstrap();
        $ghost = new IGhost(
            $this->container,
            $this->chatbotConfig,
            $this->logInfo,
            $this->consoleLogger
        );

        return $ghost;
    }

    public function getShell(string $shellName): Shell
    {
        $this->bootstrap();
        foreach ($this->chatbotConfig->shells as $shell) {
            if ($shellName === $shell->shellName) {
                return new IShell(
                    $this->container,
                    $this->chatbotConfig,
                );

            }
        }
    }


}