<?php

/**
 * Class Server
 * @package Commune\Chatbot\App\Platform\ReactorStdio
 */

namespace Commune\Chatbot\App\Platform\ReactorStdio;


use Commune\Chatbot\App\Platform\ConsoleConfig;
use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Contracts\ChatServer;
use Commune\Chatbot\App\Messages\Events\ConnectionEvt;
use React\EventLoop\Factory;
use Clue\React\Stdio\Stdio;
use React\EventLoop\LoopInterface;

class StdioServer implements ChatServer
{

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var Stdio
     */
    protected $stdio;

    protected $output;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->loop = Factory::create();
        $this->stdio = new Stdio($this->loop);
    }

    public function sleep(int $millisecond): void
    {
        usleep($millisecond * 1000);
    }


    public function run(): void
    {
        $this->app->bootApp();
        $this->stdio->getReadline()->setPrompt('> ');

        $config = $this->app->getProcessContainer()[ConsoleConfig::class];

        $this->stdio->on('data', function ($line) use ($config){
            $line = rtrim($line, "\r\n");

            $this->app
                ->getKernel()
                ->onUserMessage(
                    new StdioUserMessageRequest($line, $this->stdio, $config)
                );
        });

        $this->app->getKernel()->onUserMessage(
            new StdioUserMessageRequest(new ConnectionEvt(), $this->stdio, $config)
        );

        $this->loop->run();
    }

    public function fail(): void
    {
        $this->stdio->end("error occur, exit \n");
    }

    public function closeClient(Conversation $conversation): void
    {
        $this->stdio->end("bye! \n");
    }

    protected $available = true;

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailable(bool $boolean): void
    {
        $this->available = $boolean;
    }


}