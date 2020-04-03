<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Blueprint\Session;

use Commune\Framework\Blueprint\Intercom\GhostInput;
use Commune\Framework\Blueprint\Session\Session;
use Commune\Framework\Contracts\Cache;
use Commune\Framework\Contracts\Messenger;
use Commune\Framework\Blueprint\ReqContainer;
use Commune\Framework\Blueprint\Intercom\ShellInput;
use Commune\Framework\Blueprint\Intercom\ShellOutput;
use Commune\Message\Blueprint\Message;
use Commune\Shell\Blueprint\Event\ShellEvent;
use Commune\Shell\Blueprint\Render\Renderer;
use Commune\Shell\Blueprint\Shell;
use Commune\Shell\Contracts\ShlRequest;
use Commune\Shell\Contracts\ShlResponse;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 以下组件可以依赖注入
 *
 * @property-read Shell $shell                  获取 Shell 本身.
 *
 * 请求相关
 *
 * @property-read ReqContainer $container       容器
 * @property-read ShlRequest $request           当前的请求.
 * @property-read ShlResponse $response         当前请求的响应
 * @property-read ShlSessionLogger $logger      会话自己的日志, 会记录 Req 相关信息.
 * @property-read ShellInput $shellInput
 * @property-read GhostInput $ghostInput
 *
 * 请求级单例
 *
 * @property-read Cache $cache                  缓存
 * @property-read Renderer $renderer
 * @property-read Messenger $messenger
 * @property-read ShlSessionStorage $storage
 */
interface ShlSession extends Session
{

    /**
     * @param ShellOutput[] $outputs
     */
    public function addShellOutputs(array $outputs) : void;

    /**
     * @return ShellOutput[]
     */
    public function getShellOutputs() : array;

}