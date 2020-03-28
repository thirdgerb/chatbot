<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Bootstrap;

use Commune\Framework\Blueprint\App;
use Commune\Framework\Contracts\Bootstrapper;
use Commune\Framework\Contracts\ConsoleLogger;
use Commune\Ghost\Blueprint\Ghost;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class BootGhost implements Bootstrapper
{
    /**
     * @var ConsoleLogger
     */
    protected $console;

    /**
     * @var Ghost
     */
    protected $ghost;

    /**
     * BootGhost constructor.
     * @param ConsoleLogger $console
     * @param Ghost $ghost
     */
    public function __construct(ConsoleLogger $console, Ghost $ghost)
    {
        $this->console = $console;
        $this->ghost = $ghost;
    }


    public function bootstrap(): void
    {
        $botName = $this->ghost->getChatbotName();
        $info =
<<<EOF
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
                                                            
Boot Commune Chatbot Ghost

ChatbotName: $botName
                                                    
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
EOF;

        $this->console->info($info);
    }
}