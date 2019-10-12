<?php


namespace Commune\Demo\App\Providers;

use Commune\Chatbot\Framework\Events\ChatbotPipeClose;
use Commune\Chatbot\Framework\Events\ChatbotPipeStart;
use Commune\Chatbot\Framework\Providers\EventServiceProvider as Example;

class EventServiceProvider extends Example
{
    protected $events = [
        ChatbotPipeStart::class => [

        ],
        ChatbotPipeClose::class => [
        ],

    ];

    public function register(): void
    {
    }
}