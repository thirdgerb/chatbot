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

use Commune\Blueprint\Ghost\Mindset;
use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Contracts\ServiceProvider;
use Commune\Ghost\IMindset;
use Commune\Support\Registry\OptRegistry;
use Psr\Log\LoggerInterface;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read bool $initializeContexts  是否要在初始化的时候, 主动注册所有的 Context. 这样可以确保绝大多数 Intent 和 Stage 都得到注册.
 *
 * @property-read int $defCacheExpire
 */
class MindsetServiceProvider extends ServiceProvider
{
    public static function stub(): array
    {
        return [
            'initializeContexts' => true,
            'defCacheExpire' => 599,
        ];
    }

    public function boot(ContainerContract $app): void
    {
        $logger = $this->getLogger($app);
        // 主动跑一次所有 Context 的初始化.
        if ($this->initializeContexts) {
            /**
             * @var Mindset $mindset
             */
            $mindset = $app->get(Mindset::class);
            $mindset->initContexts($logger);
        }
    }

    protected function getLogger(ContainerContract $app) : LoggerInterface
    {
        return $app->get(ConsoleLogger::class);
    }

    public function register(ContainerContract $app): void
    {
        $app->singleton(Mindset::class, function(ContainerContract $app){
            $optRegistry = $app->get(OptRegistry::class);
            return new IMindset($optRegistry, $this->defCacheExpire);
        });
    }


}