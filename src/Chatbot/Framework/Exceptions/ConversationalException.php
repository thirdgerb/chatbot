<?php

/**
 * Class LogicExceptionInterface
 * @package Commune\Chatbot\Framework\Exceptions
 */

namespace Commune\Chatbot\Framework\Exceptions;

/**
 * 对话级别的异常.
 * 不影响下一轮对话.
 */
class ConversationalException extends ChatbotRuntimeException
{
}