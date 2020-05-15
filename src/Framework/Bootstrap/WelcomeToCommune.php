<?php

/**
 * Class WelcomeToUserChatbot
 * @package Commune\Framework\Bootstrap
 */

namespace Commune\Framework\Bootstrap;


use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Framework\Bootstrapper;

class WelcomeToCommune implements Bootstrapper
{
    protected static $booted = false;

    public function bootstrap(App $app): void
    {
        if (self::$booted) {
            return;
        }
        self::$booted = true;

        $logger = $app->getConsoleLogger();
        $logger->info('@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@');
        $logger->info('');
        $logger->info('             Initialize Commune Chatbot             ');
        $logger->info('');
        $logger->info('@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@');
        $logger->info('');
    }


}