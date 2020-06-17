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
use Commune\Blueprint\Host;
use Commune\Blueprint\Platform;
use Commune\Support\Struct\AbsStruct;
use React\EventLoop\Factory;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $shellName
 * @property-read string $guestId
 * @property-read string $guestName
 */
class StdioDemoPlatform extends AbsStruct implements Platform
{
    /**
     * @var Host
     */
    protected $host;

    /**
     * @var HostConfig
     */
    protected $config;

    public function __construct(Host $host, HostConfig $hostConfig, array $config)
    {
        $this->host = $host;
        $this->config = $hostConfig;
        parent::__construct($config);
    }

    public static function stub(): array
    {
        return [
            'shellName' => 'stdioTestName',
            'guestId' => 'stdioTestGuestId',
            'guestName' => 'stdioTestGuestName',
        ];
    }

    public static function relations(): array
    {
        return [];
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