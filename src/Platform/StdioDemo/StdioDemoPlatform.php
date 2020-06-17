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
use Commune\Blueprint\Ghost;
use Commune\Blueprint\Host;
use Commune\Blueprint\Platform;
use Commune\Blueprint\Shell;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;


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
     * @var Shell
     */
    protected $shell;

    /**
     * @var StdioDemoOption
     */
    protected $option;

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var Stdio
     */
    protected $stdio;

    public function __construct(
        Host $host,
        StdioDemoOption $option
    )
    {
        $this->host = $host;
        $this->option = $option;

        $procC = $host->getProcContainer();
        $this->shell = $procC->get(Shell::class);

        $this->loop = Factory::create();
        $this->stdio = new Stdio($this->loop);
    }

    public function serve(): void
    {
        $this->stdio->setPrompt('> ');
        // each message
        $this->stdio->on('data', [$this, 'onData']);
        $this->loop->run();
    }

    public function onData($line) : void
    {

        try {

            $adapter = new StdioDemoAdapter($this->stdio, $line);
//            $request = $adapter->getRequest();
//            $response = $this->shell->handleRequest($request);
//            $adapter->sendResponse($response);

            $this->stdio->write('hello.world');

        } catch (\Throwable $e) {
            $this->catchExp($e);

        }

    }

    public function sleep(float $seconds): void
    {
        usleep(intval($seconds * 1000));
    }

    public function catchExp(\Throwable $e): void
    {
        $this->host->getConsoleLogger()->error(strval($e));
    }


    public function shutdown(): void
    {
        exit();
    }


}