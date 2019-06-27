<?php



return [
    'debug' => true,
    'configBindings' => [
        \Commune\Chatbot\App\Platform\ConsoleConfig::class,
    ],
    'components' => [
        \Commune\Demo\App\DemoOption::class,
        \Commune\Chatbot\App\Components\ConfigurableComponent::class,
        \Commune\Chatbot\App\Components\NLUExamplesComponent::class => [
            'repository' => __DIR__ .'/repository.json'
        ],
    ],
    'reactorProviders' => [
        \Commune\Chatbot\App\Platform\ReactorStdio\RSServerServiceProvider::class,
        \Commune\Chatbot\App\Drivers\Demo\ExpHandlerServiceProvider::class,
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
            'name' => 'chatbot',
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
        
    'host' => [
        'rootContextName' => \Commune\Demo\App\Contexts\Welcome::class,
        'navigatorIntents' => [
            \Commune\Demo\App\Intents\QuitInt::class
        ]
    ] + \Commune\Chatbot\Config\Host\OOHostConfig::stub(),

];