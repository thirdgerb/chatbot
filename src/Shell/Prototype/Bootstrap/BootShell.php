<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Prototype\Bootstrap;

use Commune\Framework\Contracts\Bootstrapper;
use Commune\Framework\Contracts\ConsoleLogger;
use Commune\Shell\Blueprint\Shell;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class BootShell implements Bootstrapper
{
    /**
     * @var Shell
     */
    protected $shell;

    /**
     * @var ConsoleLogger
     */
    protected $console;

    /**
     * BootShell constructor.
     * @param Shell $shell
     * @param ConsoleLogger $console
     */
    public function __construct(Shell $shell, ConsoleLogger $console)
    {
        $this->shell = $shell;
        $this->console = $console;
    }


    public function bootstrap(): void
    {
        $botName = $this->shell->getChatbotName();
        $shellName = $this->shell->getShellName();
        $info =
            <<<EOF
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
                                                            
Boot Commune Chatbot Ghost

ChatbotName: $botName
ShellName: $shellName
                                                    
@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
EOF;

        $this->console->info($info);
    }


}