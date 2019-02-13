<?php

/**
 * Class Kernel
 * @package Commune\Chatbot\Server
 */

namespace Commune\Chatbot\Framework;


use Carbon\Carbon;
use Commune\Chatbot\Contracts\ChatbotKernel;
use Commune\Chatbot\Contracts\ExceptionHandler;
use Commune\Chatbot\Contracts\IdGenerator;
use Commune\Chatbot\Contracts\ServerDriver;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Conversation\IncomingMessage;
use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Framework\Exceptions\ChatbotException;
use Psr\Log\LoggerInterface;
use Commune\Chatbot\Framework\Support\Pipeline;


class Kernel implements ChatbotKernel
{
    /**
     * @var ChatbotApp
     */
    protected $app;


    /**
     * @var ServerDriver
     */
    protected $driver;

    /**
     * @var ExceptionHandler
     */
    protected $expHandler;


    /**
     * @var LoggerInterface
     */
    protected $log;

    protected $booted = false;


    /**
     * @var Pipeline
     */
    protected $pipeline;


    /**
     * @var IdGenerator
     */
    protected $idGenerator;

    public function __construct(
        ChatbotApp $app,
        LoggerInterface $log,
        IdGenerator $idGenerator,
        ExceptionHandler $exceptionHandler
    )
    {
        $this->app = $app;
        $this->log = $log;
        $this->idGenerator = $idGenerator;
        $this->expHandler = $exceptionHandler;
        $this->bootstrap();
    }


    /**
     * Bootstrap the application
     * @return void
     */
    public function bootstrap()
    {
        if (! $this->booted) {
            foreach ($this->app->getConfig(ChatbotApp::RUNTIME_BOOTSTRAPPERS) as $bootstrapper) {
                $this->app->make($bootstrapper)->bootstrap($this->app);
            }
            $this->booted = true;
        }
    }


    public function handle($request)
    {
        $driver = $this->app->getServerDriver();
        try {

            $sender = $driver->fetchSender($request);
            $recipient = $driver->fetchRecipient($request);
            $message = $driver->fetchMessage($request);
            $messageId = $this->idGenerator->makeMessageUUId();
            $platform = $driver->getPlatform();

            $incomingMessage = new IncomingMessage(
                $messageId,
                $sender,
                $recipient,
                $platform,
                $message,
                new Carbon()
            );

            $chatId = $this->idGenerator->makeChatId(
                $sender->getId(),
                $recipient->getId(),
                $platform->getId()
            );

            $conversation = new Conversation($incomingMessage, $chatId);
            /**
             * @var Conversation $replyConversation
             */
            $replyConversation = $this->getPipeline()->send($conversation);

            $driver->reply($replyConversation);

            if ($replyConversation->isCloseSession()) {
                $driver->close();
            }

        } catch (\Exception $e) {
            $this->expHandler->handle($e);
            $driver->error($e);
        }
    }

    protected function getPipeline() : Pipeline
    {
        if (!isset($this->pipeline)) {
            $this->pipeline = new Pipeline(
                $this->app,
                $this->app->getConfig(ChatbotApp::RUNTIME_PIPES, []),
                function(Conversation $conversation){
                    return $conversation;
                }
            );
        }

        return $this->pipeline;
    }

}