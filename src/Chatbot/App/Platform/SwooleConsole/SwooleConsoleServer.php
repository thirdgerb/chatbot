<?php


namespace Commune\Chatbot\App\Platform\SwooleConsole;


use Commune\Chatbot\App\Platform\ConsoleConfig;
use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Contracts\ChatServer;
use Commune\Chatbot\App\Messages\Events\ConnectionEvt;
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
     * @var array
     */
    protected $allow;

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
        $config = $app->getProcessContainer()[ConsoleConfig::class];
        $this->ip = $config->ip;
        $this->port = $config->port;
        $this->allow = $config->allowIPs;
        $this->server = new Server($this->ip, $this->port);
    }

    protected function bootstrap() : void
    {
        Runtime::enableCoroutine();
        $config = $this->app->getProcessContainer()[ConsoleConfig::class];

        $this->server->on('connect', function (Server $server, $fd) use ($config){
            $info = $server->getClientInfo($fd);
            $address = $info['remote_ip'] ?? '';
            if (in_array($address, $this->allow)) {
                echo "connection open: {$address} {$fd}\n";

                $kernel = $this->app->getKernel();
                $request = new SwooleUserMessageRequest(
                    $server,
                    $fd,
                    new ConnectionEvt(),
                    $config
                );
                $kernel->onUserMessage($request);

            } else {
                echo "connection not allowed: {$address} {$fd}\n";
                $server->send($fd, "ip not allowed\n");
                $server->close($fd);
            }

        });

        $this->server->on('close', function($server, $fd) {
            echo "connection close: {$fd}\n";
        });

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