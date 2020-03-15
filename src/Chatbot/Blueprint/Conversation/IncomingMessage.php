<?php

namespace Commune\Chatbot\Blueprint\Conversation;

/**
 * 对输入消息的封装, 是 message request 对请求转义后的结果
 */
interface IncomingMessage extends ConversationMessage
{


    /*-------- 命令相关 --------*/

    /**
     * 如果一个消息可以被视作一个命令,
     * 它就有一个去掉命令标识符的 c(om)m(an)d text
     *
     * 默认的命令标识符是 "#"
     * 例如 "#help -p" 的cmdText 是 "help -p"
     *
     * 这是方便定义 command 的时候不用关心标识符.
     * 同时, 匹配标识符的方法不需要执行多次.
     *
     * @return null|string
     */
    public function getCmdText() : ? string;

    /**
     * @param string|null $text
     */
    public function setCmdText(string $text = null) : void;

}