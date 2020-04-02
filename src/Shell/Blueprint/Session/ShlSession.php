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
use Commune\Framework\Contracts\Cache;
use Commune\Framework\Contracts\Messenger;
use Commune\Framework\Blueprint\ReqContainer;
use Commune\Framework\Blueprint\Intercom\ShellInput;
use Commune\Framework\Blueprint\Intercom\ShellOutput;
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
interface ShlSession
{

    /**
     * @return string
     */
    public function getChatId() : string;

    /**
     * @return bool
     */
    public function isFinished() : bool;

    /*------- setter -------*/

    public function setProperty(string $name, $object) : void;

    /*------- i/o -------*/

    /**
     * @param ShellOutput[] $outputs
     */
    public function setShellOutputs(array $outputs) : void;

    /**
     * @return ShellOutput[]
     */
    public function getShellOutputs() : array;

    /*------- finish -------*/

    /**
     * 结束 Session, 处理垃圾回收
     */
    public function finish() : void;


    /*------- 事件机制 -------*/

    /**
     * 触发一个 Session 事件.
     * @param ShellEvent $event
     */
    public function fire(ShellEvent $event) : void;

    /**
     * @param string $eventName
     * @param callable $handler  function(ShlSession $session, SessionEvent $event){}
     */
    public function listen(string $eventName, callable $handler) : void;

}