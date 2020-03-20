<?php


namespace Commune\Chatbot\App\Messages\Templates;


use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\ReplyTemplate;
use Commune\Chatbot\Blueprint\Message\ReplyMsg;
use Commune\Chatbot\Config\ChatbotConfig;

class MissedTemp implements ReplyTemplate
{

    /**
     * @var ChatbotConfig
     */
    protected $chatbotConfig;

    /**
     * MissedTemp constructor.
     * @param ChatbotConfig $chatbotConfig
     */
    public function __construct(ChatbotConfig $chatbotConfig)
    {
        $this->chatbotConfig = $chatbotConfig;
    }


    public function render(ReplyMsg $reply, Conversation $conversation): array
    {
        $nluReplies = $conversation->getNLU()->getDefaultReplies();
        if (!empty($nluReplies)) {
            return $nluReplies->toArray();
        }
        $text = $this->chatbotConfig->defaultMessages->messageMissMatched;
        return [new Text($conversation->getSpeech()->trans($text))];
    }


}