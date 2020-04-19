<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Prototype\Servers;

use Clue\React\Stdio\Stdio;
use Commune\Framework\Blueprint\Server;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AReactStdioServer implements Server
{

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var Stdio
     */
    protected $stdio;


    public function __construct()
    {
        $this->loop = Factory::create();
        $this->stdio = new Stdio($this->loop);
    }

    abstract public function handleData(string $data) : void;

    public function getId(): string
    {
        return php_uname('n');
    }


    public function coSleep(float $seconds): void
    {
        usleep($seconds);
    }

    public function start(): void
    {
        $stdio = $this->stdio;
        $loop = $this->loop;
        $stdio->setPrompt('> ');
        $stdio->on('data', function($data){
            try {
                $this->handleData($data);
            } catch (\Throwable $e) {
                $this->catchExp($e);
            }
        });
        $loop->run();
    }


    public function catchExp(\Throwable $e): void
    {
        $this->stdio->end('end');
    }


}