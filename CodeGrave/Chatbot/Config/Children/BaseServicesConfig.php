<?php


namespace Commune\Chatbot\Config\Children;

use Commune\Chatbot\Framework\Providers;
use Commune\Chatbot\OOHost\HostConversationalServiceProvider;
use Commune\Chatbot\OOHost\HostProcessServiceProvider;
use Commune\Support\Option;

/**
 * 系统的基础服务. 可以修改, 但要注意 process service 和 conversation service 的区别.
 *
 * process level :
 * @property-read string $exp 异常上报服务.
 * @property-read string $translation 翻译模块的服务.
 * @property-read string $render 渲染模块的服务
 * @property-read string $logger 日志模块
 * @property-read string $event 事件服务.注册在 process, 但运行时必须通过 $conversation->fire
 * @property-read string $soundLike 语音模糊匹配.
 * @property-read string $hostProcess host 的 process 级服务
 *
 * conversation level:
 * @property-read string $conversational 系统默认的会话级服务.
 * @property-read string $hostConversation host 的 conversation 级服务
 */
class BaseServicesConfig extends Option
{
    public static function stub(): array
    {
        return [
            'exp' => Providers\ExceptionReporterServiceProvider::class,
            'optionRepo' => Providers\OptionRepoServiceProvider::class,
            'translation' => Providers\TranslatorServiceProvider::class,
            'render' => Providers\ReplyRendererServiceProvider::class,
            'logger' => Providers\LoggerServiceProvider::class,
            'event' => Providers\EventServiceProvider::class,
            'soundLike' => Providers\SoundLikeServiceProvider::class,
            'conversational' => Providers\ConversationalServiceProvider::class,
            'hostProcess' => HostProcessServiceProvider::class,
            'hostConversation' => HostConversationalServiceProvider::class,
        ];
    }


}