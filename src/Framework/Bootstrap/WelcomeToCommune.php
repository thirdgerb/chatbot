<?php

/**
 * Class WelcomeToUserChatbot
 * @package Commune\Framework\Bootstrap
 */

namespace Commune\Framework\Bootstrap;

use Commune\Framework\Blueprint\Bootstrapper;
use Commune\Shell\Blueprint\Shell;

class WelcomeToCommune implements Bootstrapper
{


    public function boot(Shell $shell): void
    {
        $logger = $shell->getServer()->getConsole();
        $logger->info('');
        $logger->info("@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@");
        $logger->info("                                                    ");
        $logger->info("                Boot Commune Chatbot                ");
        $logger->info("                                                    ");
        $logger->info("@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@");
        $logger->info('');
    }


}