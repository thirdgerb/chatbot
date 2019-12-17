<?php


namespace Commune\Chatbot\Framework\Exceptions;


/**
 * 需要关闭会话的异常.
 * 通常是多轮对话的错误, 导致多轮对话状态再也无法恢复了.
 * 如果不关闭 Session 就会产生死循环.
 */
class CloseSessionException extends ChatbotRuntimeException
{
}