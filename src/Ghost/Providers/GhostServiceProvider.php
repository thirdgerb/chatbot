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

use Commune\Container\ContainerContract;
use Commune\Framework\Contracts\ServiceProvider;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class GhostServiceProvider extends ServiceProvider
{

    public static function stub(): array
    {
        return [];
    }


    public function boot(ContainerContract $app): void
    {
    }

    public function register(ContainerContract $app): void
    {
        $this->registerConvo($app);
        $this->registerConvoLogger($app);

        $this->registerScene($app);
        $this->registerScope($app);

        $this->registerAuth($app);
        $this->registerMindset($app);
        $this->registerRuntime($app);
    }

}