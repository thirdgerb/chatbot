<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocols\HostMsg\Convo;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Protocols\HostMsg\ConvoMsg;


/**
 * 用于同步状态的消息
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read array $query
 */
interface ContextMsg extends ConvoMsg
{

    // 强行替换掉当前状态.
    const MODE_REDIRECT = 0;
    // blocking 当前状态. 防止当前对话状态丢失.
    const MODE_BLOCKING = 1;
    // 标记这个语境是要取消的.
    const MODE_CANCEL = 2;
    // 标记这个语境是 fulfilled
    const MODE_FULFILL = 3;

    /*----- ucl 相关参数 -----*/

    public function getContextId() : string;

    public function getContextName() : string;

    public function getStageName() : string;

    public function getQuery() : array;

    /*----- context 数据. -----*/

    /**
     * 这里是 Context 可以缓存的数据.
     * 用这些数据理论上可以在不同的 Session 里复制 Context.
     * 因为不同的 clone session 记忆是隔离的.
     *
     * 但总有不能通过这种办法复制的情况.
     *
     * @return array
     */
    public function getMemorableData() : array;

    /**
     * 基于当前的数据, 重新生成一个 context 对象.
     *
     * @param Cloner $cloner
     * @return Context
     */
    public function toContext(Cloner $cloner) : Context;

    /*----- mode -----*/

    /**
     * ContextMsg 的模式.
     * 决定它投递到不同的 cloner 后的影响效果.
     *
     * @return int
     */
    public function getMode() : int;

    /**
     * 设置一个 contextMsg 的模式.
     * 由于模式和投递场景相关, 所以这个方法不定义在 Context::toContextMsg() 里.
     *
     * @param int $mode
     * @return ContextMsg
     */
    public function withMode(int $mode) : self;
}