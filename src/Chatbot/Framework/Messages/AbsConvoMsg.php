<?php


namespace Commune\Chatbot\Framework\Messages;


/**
 * 本意在 conversation 中传递的 message.
 * 区别于 Context . 因为 Context 也被视作一种 Message
 * Context 是 Session 相关的. 虽然也可能在 Conversation 中传递,
 *
 * 也可以理解成所有非 Context 的 message
 */
abstract class AbsConvoMsg extends AbsMessage
{

    /**
     * 所有可传输的消息都要允许 mock, 从而可进行统一的单元测试.
     * @return static
     */
    abstract public static function mock();
}