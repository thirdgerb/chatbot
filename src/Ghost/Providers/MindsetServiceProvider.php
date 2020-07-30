<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Providers;

use Commune\Blueprint\CommuneEnv;
use Commune\Blueprint\Configs\GhostConfig;
use Commune\Blueprint\Ghost\Mindset;
use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Contracts\ServiceProvider;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class MindsetServiceProvider extends ServiceProvider
{
    public static function stub(): array
    {
        return [];
    }

    public function getDefaultScope(): string
    {
        return self::SCOPE_PROC;
    }

    public function boot(ContainerContract $app): void
    {

        // self Register
        /**
         * @var GhostConfig $config
         * @var Mindset $mindset
         * @var ConsoleLogger $logger
         */
        $config = $app->get(GhostConfig::class);
        $mindset = $app->get(Mindset::class);
        $logger = $app->get(ConsoleLogger::class);

        if (CommuneEnv::isResetRegistry()) {
            $logger->warning("reset all mindset data!!");
            $mindset->reset();
        }

        // 加载项目 默认的 self register
        foreach ($config->mindPsr4Registers as $namespace => $path) {
            Psr4SelfRegisterLoader::loadSelfRegister(
                $mindset,
                $namespace,
                $path,
                $logger
            );
        }
    }

    public function register(ContainerContract $app): void
    {
    }


}