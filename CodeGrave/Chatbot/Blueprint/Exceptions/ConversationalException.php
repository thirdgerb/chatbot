<?php

/**
 * Class LogicExceptionInterface
 * @package Commune\Chatbot\Blueprint\Exceptions
 */

namespace Commune\Chatbot\Blueprint\Exceptions;

/**
 * 对话级别的异常.
 * 不影响下一轮对话.
 */
class ConversationalException extends ChatbotRuntimeException
{
}