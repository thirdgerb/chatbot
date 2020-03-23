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

use Commune\Chatbot\Contracts\Cache;
use Commune\Chatbot\Contracts\Messenger;
use Commune\Framework\Blueprint\App;
use Commune\Shell\Blueprint\Kernels\RequestKernel;
use Commune\Shell\Blueprint\Render\Renderer;
use Commune\Shell\Contracts\ShlServer;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 相关属性
 *
 * @property-read string $shellName             Shell 的名称
 *
 * 以下属性可以依赖注入
 *
 * @property-read ShellConfig $config           Shell 的配置
 * @property-read Renderer $renderer            消息渲染模板
 * @property-read Messenger $messenger          消息发送工具
 * @property-read ShlServer $server             服务端的实例
 * @property-read Cache $cache                  公共的 Cache
 *
 */
interface Shell extends App
{
    /**
     * @return RequestKernel
     */
    public function getReqKernel() : RequestKernel;

}