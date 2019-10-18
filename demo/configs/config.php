<?php



return [

    'chatbotName' => 'demo',

    'debug' => true,

    'configBindings' => [
        \Commune\Chatbot\App\Platform\ConsoleConfig::class => [
            'allowIPs' => ['127.0.0.1'],
        ],
    ],
    'components' => [
        \Commune\Components\Demo\DemoComponent::class,
        \Commune\Components\SimpleChat\SimpleChatComponent::class,
        \Commune\Components\Story\StoryComponent::class,
        \Commune\Components\Rasa\RasaComponent::class,

//        \Commune\Chatbot\App\Components\SimpleFileChatComponent::class,
    ],
    'processProviders' => [
    ],
    'conversationProviders' => [
        \Commune\Chatbot\App\Drivers\Demo\CacheServiceProvider::class,
        \Commune\Chatbot\App\Drivers\Demo\SessionServiceProvider::class,
    ],
    'chatbotPipes' =>
        [
            'onUserMessage' => [
                \Commune\Chatbot\App\ChatPipe\MessengerPipe::class,
                \Commune\Chatbot\App\ChatPipe\ChattingPipe::class,
                \Commune\Chatbot\OOHost\OOHostPipe::class,
            ],
        ],
    'translation' =>
        [
            'loader' => 'php',
            'resourcesPath' => __DIR__ . '/../../src/Chatbot/App/trans',
            'defaultLocale' => 'zh',
            'cacheDir' => NULL,
        ],
    'logger' =>
        [
            'path' => __DIR__ . '/cache/tmp.log',
            'days' => 0,
            'level' => 'debug',
            'bubble' => true,
            'permission' => NULL,
            'locking' => false,
        ],
    'defaultMessages' =>
        [
            'platformNotAvailable' => 'system.platformNotAvailable',
            'chatIsTooBusy' => 'system.chatIsTooBusy',
            'systemError' => 'system.systemError',
            'farewell' => 'dialog.farewell',
            'messageMissMatched' => 'dialog.missMatched',
        ],
    'eventRegister' =>[
        
    ],

    'defaultSlots' => [
        // 系统默认的slots, 所有的reply message 都会使用
        // 多维数组会被抹平为 self.name 这样的形式
        // default reply slots
        // multi-dimension array will be flatten to dot pattern
        // such as 'self.name'
        'self' => [
            'name' => 'CommuneChatbot',
            'project' => 'commune/chatbot',
            'fullname' => 'commune/chatbot demo',
            'author' => 'thirdgerb',
            'email' => 'thirdgerb@gmail.com',
            'desc' => '多轮对话机器人开发框架',
        ]
    ],
        
    'host' => [
        'rootContextName' => \Commune\Components\Demo\Contexts\DemoHome::class,
        'sessionPipes' => [
            \Commune\Chatbot\App\SessionPipe\EventMsgPipe::class,
            \Commune\Chatbot\App\SessionPipe\MarkedIntentPipe::class,
            \Commune\Chatbot\App\Commands\UserCommandsPipe::class,
            \Commune\Chatbot\App\Commands\AnalyserPipe::class,

            \Commune\Components\Rasa\RasaSessionPipe::class,

            \Commune\Chatbot\App\SessionPipe\NavigationPipe::class,
        ],

        'hearingFallback' => null,
    ] ,

];