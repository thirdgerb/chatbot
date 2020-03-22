<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Blueprint;

use Commune\Support\Structure;


/**
 * Shell 的配置
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $name
 */
class ShellConfig extends Structure
{
    const IDENTITY = 'name';

    public static function stub(): array
    {
        return [
            // shell 的名称
            'name' => '',

            // 运行管道
            'pipeline' => [
            ],

            // 请求进入的场景
            'Topics' => [
            ],

            // shell 的内核
            'kernel' => '',

            'server' => [

            ],

            'session' => [
                'tracking' => true,
                'processExpires' => 3600,
                'stackOverflow' => 20,
            ],

            // 为 shell 注册的服务
            'providers' => [
            ],

            // 绑定的 Option 单例
            'options' => [
            ],

            // 翻译组件的配置
            'translation' => [
            ],

            // 日志模块的配置
            'logger' => [
            ],

            // 默认的消息, 功能性的消息
            'defaultReplies' => [
            ],

            // reply 的渲染.
            'replyTemplates' => [
                // replyId (prefix) => template
                // '*' => defaultTemplate
            ],

        ];
    }

}