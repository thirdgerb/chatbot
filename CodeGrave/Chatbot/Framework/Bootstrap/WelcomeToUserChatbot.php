<?php

/**
 * Class WelcomeToUserChatbot
 * @package Commune\Chatbot\Framework\Bootstrap
 */

namespace Commune\Chatbot\Framework\Bootstrap;


use Commune\Chatbot\Blueprint\Application;

/**
 * 启动程序时给用户的欢迎界面.
 * 回头好好研究怎么画画.
 *
 * Class WelcomeToUserChatbot
 * @package Commune\Chatbot\Framework\Bootstrap
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class WelcomeToUserChatbot implements Bootstrapper
{
    public function bootstrap(Application $app): void
    {
        $logger = $app->getConsoleLogger();

        $logger->info('');
        $logger->info("@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@");
        $logger->info("                                                    ");
        $logger->info("                Boot Commune Chatbot                ");
        $logger->info("                                                    ");
        $logger->info("@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@");
        $logger->info('');
    }

}