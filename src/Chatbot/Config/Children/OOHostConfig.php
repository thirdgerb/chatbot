<?php


namespace Commune\Chatbot\Config\Children;

use Commune\Chatbot\Config\Options\MemoryOption;
use Commune\Support\Option;
use Commune\Chatbot\App\SessionPipe;
use Commune\Chatbot\App\Commands;

/**
 * @property-read int $maxBreakpointHistory 单个会话可追溯的历史记录个数.| max breakpoint num record in history of one session
 *
 * @property-read int $maxRedirectTimes 单次请求最大重定向次数, 超过就怀疑发生循环定向.| max redirect times for one request. over it will trigger too many redirect exception
 *
 * @property-read int $sessionExpireSeconds 会话过期的时间.|if no request come in, session will expire at ...
 *
 * @property-read string $rootContextName 根语境的名字.
 *
 * @property-read array $autoloadPsr4 按psr-4 规范预加载context| autoload path to register context class by psr-4 pattern
 *
 * @property-read string[] $sessionPipes  消息进入会话后经过的管道.
 *
 * @property-read MemoryOption[] $memories 预定义的memory
 *
 * @property-read callable|string|null $hearingFallback  Hearing环节默认的fallback单元.
 *
 * @property-read array $slots  默认的slots.方便逻辑调用| environment slots. multidimensional array will flatten to key-value array ([a][b][c] to a.b.c)
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
            'autoloadPsr4' => [],
            'sessionPipes' => [
                SessionPipe\EventMsgPipe::class,
                Commands\UserCommandsPipe::class,
                Commands\AnalyserPipe::class,
                SessionPipe\MarkedIntentPipe::class,
                SessionPipe\NavigationPipe::class,
            ],
            'navigatorIntents' => [
                //intentName
            ],
            'hearingFallback' => null,
            'memories' => [
                 MemoryOption::stub()
            ],
            'slots' => [

            ],
        ];
    }

}