<?php


namespace Commune\Chatbot\App\Platform\Swoole;


use Commune\Chatbot\App\Platform\ConsoleConfig;
use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Contracts\ChatServer;
use Swoole\Coroutine;
use Swoole\Runtime;
use Swoole\Server;

class SwooleConsoleServer implements ChatServer
{

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var string
     */
    protected $ip;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var Server
     */
    protected $server;

    /**
     * SwooleServer constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        /**
         * @var ConsoleConfig $config
         */
        $config = $app->getReactorContainer()[ConsoleConfig::class];
        $this->ip = $config->ip;
        $this->port = $config->port;
        $this->server = new Server($this->ip, $this->port);

        Runtime::enableCoroutine();
    }



    public function run(): void
    {
        $config = $this->app->getReactorContainer()[ConsoleConfig::class];
        $this->server->on('receive', function ($server, $fd, $reactor_id, $data) use ($config) {
            $kernel = $this->app->getKernel();

            $request = new SwooleUserMessageRequest(
                $server,
                $fd,
                $data,
                $config
            );

            $kernel->onUserMessage($request);
        });

        $this->server->start();
    }

    public function sleep(int $millisecond): void
    {
        Coroutine::sleep($millisecond/1000);
    }

    public function fail(): void
    {
        exit(255);
    }

    public function closeClient(Conversation $conversation): void
    {
        /**
         * @var SwooleUserMessageRequest $request
         */
        $request = $conversation[SwooleUserMessageRequest::class];
        $this->server->close($request->getFd());
    }


}