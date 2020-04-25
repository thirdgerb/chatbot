<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost;

use Commune\Blueprint\Configs\GhostConfig;
use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Framework\ServiceRegistrar;
use Commune\Blueprint\Ghost;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\GhostKernel;
use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Contracts\Log\LogInfo;
use Commune\Framework\AbsApp;
use Commune\Protocals\Intercom\GhostInput;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IGhost extends AbsApp implements Ghost
{

    /**
     * @var GhostConfig
     */
    protected $config;

    /**
     * IGhost constructor.
     * @param GhostConfig $config
     * @param App|null $app
     * @param ContainerContract|null $procC
     * @param bool $debug
     */
    public function __construct(
        GhostConfig $config,
        App $app = null,
        ContainerContract $procC = null,
        bool $debug = false
    )
    {
        $this->config = $config;
        $set = isset($app);

        parent::__construct(
            $set ? $app->isDebugging() : $debug,
            $set ? $app->getProcContainer() : $procC,
            $set ? $app->getReqContainer() : null,
            $set ? $app->getServiceRegistrar() : null,
            $set ? $app->getConsoleLogger() : null,
            $set ? $app->getLogInfo() : null
        );
    }

    public function getName(): string
    {
        return $this->config->name;
    }


    protected function basicBindings(): void
    {
        $this->instance(GhostConfig::class, $this->config);
        $this->instance(Ghost::class, $this);
    }

    public function getConfig(): GhostConfig
    {
        return $this->config;
    }

    public function newCloner(GhostInput $input): Cloner
    {
        // MessageId 应该是唯一的.
        $container = $this->newReqContainerInstance($input->messageId);



    }




}