<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Host\Ghost;

use Commune\Blueprint\Configs\GhostConfig;
use Commune\Blueprint\Framework\App;
use Commune\Container\ContainerContract;
use Commune\Ghost\IGhost;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class GhostApp extends IGhost
{

    /**
     * IGhost constructor.
     * @param GhostConfig $config
     * @param ContainerContract|null $procC
     * @param App|null $app
     * @param bool $debug
     */
    public function __construct(
        GhostConfig $config,
        ContainerContract $procC = null,
        App $app = null,
        bool $debug = false
    )
    {
        $this->config = $config;
        $set = isset($app);

        parent::__construct(
            $config,
            $set ? $app->isDebugging() : $debug,
            $set ? $app->getProcContainer() : $procC,
            $set ? $app->getReqContainer() : null,
            $set ? $app->getServiceRegistrar() : null,
            $set ? $app->getConsoleLogger() : null,
            $set ? $app->getLogInfo() : null
        );
    }

}