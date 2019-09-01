<?php


namespace Commune\Demo\App\Providers;

use Commune\Chatbot\Framework\Events\ChatbotPipeClose;
use Commune\Chatbot\Framework\Events\ChatbotPipeStart;
use Commune\Chatbot\Framework\Providers\EventServiceProvider as Example;
use Commune\Demo\App\Listeners\PipeCloseListener;
use Commune\Demo\App\Listeners\PipeStartListener;

class EventServiceProvider extends Example
{
    protected $events = [
        ChatbotPipeStart::class => [
            [PipeStartListener::class, 'handle']

        ],
        ChatbotPipeClose::class => [
            [PipeCloseListener::class, 'handle']
        ],

    ];

    public function register(): void
    {
    }
}