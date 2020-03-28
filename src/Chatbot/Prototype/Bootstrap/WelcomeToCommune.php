<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Prototype\Bootstrap;

use Commune\Chatbot\ChatbotConfig;
use Commune\Framework\Contracts\Bootstrapper;
use Commune\Framework\Contracts\ConsoleLogger;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class WelcomeToCommune implements Bootstrapper
{
    /**
     * @var ConsoleLogger
     */
    protected $console;

    /**
     * @var ChatbotConfig
     */
    protected $config;

    /**
     * WelcomeToCommune constructor.
     * @param ConsoleLogger $console
     * @param ChatbotConfig $config
     */
    public function __construct(ConsoleLogger $console, ChatbotConfig $config)
    {
        $this->console = $console;
        $this->config = $config;
    }


    public function bootstrap(): void
    {
        $botName = $this->config->chatbotName;
        $debug = $this->config->debug ? 'true' : 'false';

        $info =
            <<<EOF
            
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
                                                            
             Welcome To Commune Chatbot            
                                                    
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
EOF;
        $this->console->info($info);
        $this->console->info("### boot chatbot : $botName, debug: $debug ###");
    }


}