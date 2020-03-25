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

use Commune\Framework\Contracts\Cache;
use Commune\Framework\Contracts\Messenger;
use Commune\Framework\Blueprint\ReqContainer;
use Commune\Message\Internal\InputMsg;
use Commune\Message\Internal\OutputMsg;
use Commune\Message\Internal\Scope;
use Commune\Shell\Blueprint\Shell;
use Commune\Shell\Contracts\ShlRequest;
use Commune\Shell\Contracts\ShlResponse;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 以下组件可以依赖注入
 *
 * @property-read ShlRequest $request           当前的请求.
 * @property-read ShlResponse $response         当前请求的响应
 * @property-read Shell $shell                  获取 Shell 本身.
 * @property-read ReqContainer $container       容器
 * @property-read ShlSessionLogger $logger      会话自己的日志, 会记录 Req 相关信息.
 *
 * 请求级单例
 *
 * @property-read Cache $cache                  缓存
 * @property-read Messenger $messenger
 */
interface ShlSession
{

    public function getIncomingMsg() : InputMsg;

    /**
     * 查看 SessionId 是否对应一个已知的 Scope
     * @return Scope
     */
    public function getScope() : Scope;

    /**
     * 用于变更 shell Session 对应的 internal scope, 与 ghost 通讯
     *
     * @param Scope $scope
     * @return bool
     */
    public function setScope(Scope $scope) : bool;


    /**
     * @param OutputMsg[] $replies
     */
    public function reply(array $replies) : void;

    /**
     * @return OutputMsg[]
     */
    public function getReplies() : array;

    /**
     * 结束 Session, 处理垃圾回收
     */
    public function finish() : void;
}