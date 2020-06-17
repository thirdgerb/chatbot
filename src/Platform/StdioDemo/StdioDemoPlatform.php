<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\StdioDemo;

use Clue\React\Stdio\Stdio;
use Commune\Blueprint\Configs\HostConfig;
use Commune\Blueprint\Configs\PlatformConfig;
use Commune\Blueprint\Host;
use Commune\Blueprint\Platform;
use React\EventLoop\Factory;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *

 */
class StdioDemoPlatform implements Platform
{
    /**
     * @var Host
     */
    protected $host;

    /**
     * @var StdioDemoOption
     */
    protected $option;

    public function __construct(
        Host $host,
        StdioDemoOption $option
    )
    {
        $this->host = $host;
        $this->option = $option;
    }

    public function serve(): void
    {
        $loop = Factory::create();
        $stdio = new Stdio($loop);
        $stdio->setPrompt('> ');

        // each message
        $stdio->on('data', function($line) use ($stdio) {
            $stdio->write("receive: $line");
        });

        $loop->run();
    }

    public function sleep(float $seconds): void
    {
        usleep(intval($seconds * 1000));
    }

    public function shutdown(): void
    {
        exit();
    }


}