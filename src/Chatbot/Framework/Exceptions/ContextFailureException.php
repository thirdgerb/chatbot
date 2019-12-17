<?php


namespace Commune\Chatbot\Framework\Exceptions;


/**
 * 多轮对话逻辑上的异常, 会导致当前语境退出, 类似 Dialog::cancel
 *
 * @see \Commune\Chatbot\OOHost\Directing\Backward\Failure
 */
class ContextFailureException extends ChatbotRuntimeException
{
}