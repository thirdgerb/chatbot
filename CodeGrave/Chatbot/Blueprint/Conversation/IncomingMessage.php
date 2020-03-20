<?php

namespace Commune\Chatbot\Blueprint\Conversation;

use Commune\Chatbot\Blueprint\Message\Transformed\ChoiceMsg;
use Commune\Chatbot\Blueprint\Message\Transformed\CommandMsg;
use Commune\Chatbot\Blueprint\Message\Transformed\IntentMsg;

/**
 * 对输入消息的封装, 是 message request 对请求转义后的结果
 *
 * 允许通过中间件等环节, 逐步加码.
 */
interface IncomingMessage extends ConversationMessage
{

    /*------- command 将输入消息转化为命令 -------*/

    public function setCommandMsg(CommandMsg $msg) : void;

    public function getCommandMsg() : ? CommandMsg;


    /*------- choice 将输入消息转化为选择 -------*/

    public function setChoiceMsg(ChoiceMsg $choice) : void;

    public function getChoiceMsg() : ChoiceMsg;

    /*------- intent 将输入消息转化为意图 -------*/

    public function setIntentMsg(IntentMsg $intent) :  void;

    public function getIntentMsg() : ? IntentMsg;

}