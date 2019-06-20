<?php


namespace Commune\Chatbot\App\Platform\SwooleConsole;


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
    }

    protected function bootstrap() : void
    {
        Runtime::enableCoroutine();

        $this->server->on('connect', function ($server, $fd){
            echo "connection open: {$fd}\n";
        });

        $this->server->on('close', function($server, $fd) {
            echo "connection close: {$fd}\n";
        });

        $config = $this->app->getReactorContainer()[ConsoleConfig::class];
        $this->server->on('receive', function ($server, $fd, $reactor_id, $data) use ($config) {
            $kernel = $this->app->getKernel();

            $request = new SwooleUserMessageRequest(
                $server,
                $fd,
                trim($data),
                $config
            );

            $kernel->onUserMessage($request);
        });
    }



    public function run(): void
    {
        $this->bootstrap();


        $this->server->start();
    }

    public function sleep(int $millisecond): void
    {
        Coroutine::sleep($millisecond/1000);
    }

    public function fail(): void
    {
        $this->server->shutdown();
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