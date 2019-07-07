<?php


namespace Commune\Chatbot\Config\Host;

use Commune\Chatbot\App\Commands\AnalyserPipe;
use Commune\Chatbot\App\Commands\UserCommandsPipe;
use Commune\Chatbot\App\SessionPipe\EventMsgPipe;
use Commune\Chatbot\App\SessionPipe\MarkedIntentPipe;
use Commune\Chatbot\App\SessionPipe\NavigationPipe;
use Commune\Chatbot\App\SessionPipe\DefaultReplyPipe;
use Commune\Support\Option;

/**
 * @property-read int $maxBreakpointHistory 会话可追溯的历史记录个数.
 * @property-read string $rootContextName 根语境的名字.
 * @property-read int $maxRedirectTimes 单次请求最大重定向次数, 超过就怀疑发生循环定向.
 * @property-read array $autoloadPsr4 按psr-4 规范预加载context
 * @property-read int $sessionExpireSeconds 会话过期的时间.
 * @property-read int $sessionCacheSeconds 会话数据缓存的时间. 仅仅起到缓存作用.
 * @property-read string[] $sessionPipes  会话中的管道.
 * @property-read string[] $navigatorIntents
 * @property-read MemoryOption[] $memories
 */
class OOHostConfig extends Option
{
    protected static $associations = [
        'memories[]' => MemoryOption::class,
    ];

    public static function stub(): array
    {
        return [
            'rootContextName' => 'rootContextName',
            'maxBreakpointHistory' => 5,
            'maxRedirectTimes' => 20,
            'sessionExpireSeconds' => 3600,
            'sessionCacheSeconds' => 60,
            'autoloadPsr4' => [],
            'sessionPipes' => [
                DefaultReplyPipe::class,
                EventMsgPipe::class,
                UserCommandsPipe::class,
                AnalyserPipe::class,
                MarkedIntentPipe::class,
                NavigationPipe::class,
            ],
            'navigatorIntents' => [
                //intentName
            ],
            'memories' => [
                 MemoryOption::stub()
            ]
        ];
    }

}