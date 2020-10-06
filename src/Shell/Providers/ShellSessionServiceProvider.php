<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Providers;

use Commune\Blueprint\Shell\Session\ShellLogger;
use Commune\Blueprint\Shell\Session\ShellStorage;
use Commune\Container\ContainerContract;
use Commune\Contracts\ServiceProvider;
use Commune\Shell\Render\ConfuseRenderer;
use Commune\Shell\Render\EmptyRenderer;
use Commune\Shell\Render\StageEventRenderer;
use Commune\Shell\Render\SystemIntentRenderer;
use Commune\Shell\Render\TranslatorRenderer;
use Commune\Shell\Session\IShellLogger;
use Commune\Shell\Session\IShellStorage;
use Psr\Log\LoggerInterface;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ShellSessionServiceProvider extends ServiceProvider
{
    public function getDefaultScope(): string
    {
        return self::SCOPE_REQ;
    }

    public static function stub(): array
    {
        return [];
    }

    public function boot(ContainerContract $app): void
    {
    }

    public function register(ContainerContract $app): void
    {
        $singletons = [
            ShellStorage::class => IShellStorage::class,
            ShellLogger::class => function(ContainerContract $app) {
                return new IShellLogger(
                    $app->make(LoggerInterface::class),
                    $app
                );
            },

            // 系统预定义的 renderer
            TranslatorRenderer::class => TranslatorRenderer::class,
            EmptyRenderer::class => EmptyRenderer::class,
            SystemIntentRenderer::class => SystemIntentRenderer::class,
            ConfuseRenderer::class => ConfuseRenderer::class,
            StageEventRenderer::class => StageEventRenderer::class,
        ];

        foreach ($singletons as $abstract => $concrete) {
            if (!$app->has($abstract)) {
                $app->singleton(
                    $abstract,
                    $concrete
                );
            }
        }

        unset($singletons);
    }
}