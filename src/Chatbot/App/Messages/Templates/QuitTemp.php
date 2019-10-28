<?php


namespace Commune\Chatbot\App\Messages\Templates;


use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\ReplyTemplate;
use Commune\Chatbot\Blueprint\Message\ReplyMsg;
use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Contracts\ChatServer;

class QuitTemp implements ReplyTemplate
{
    /**
     * @var ChatServer
     */
    protected $server;

    /**
     * @var ChatbotConfig
     */
    protected $config;

    /**
     * QuitTemp constructor.
     * @param ChatServer $server
     * @param ChatbotConfig $config
     */
    public function __construct(ChatServer $server, ChatbotConfig $config)
    {
        $this->server = $server;
        $this->config = $config;
    }


    public function render(ReplyMsg $reply, Conversation $conversation): array
    {
        $conversation->onFinish(function(ChatServer $server) use ($conversation){
            $server->closeClient($conversation);
        });

        $text = $this->config->defaultMessages->farewell;
        return [new Text($conversation->getSpeech()->trans($text))];
    }


}