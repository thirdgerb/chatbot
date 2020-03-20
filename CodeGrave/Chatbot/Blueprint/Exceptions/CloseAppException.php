<?php


namespace Commune\Chatbot\Blueprint\Exceptions;


/**
 * 不应该出现的异常, 要求 app 本身停止运行.
 */
class CloseAppException extends ChatbotRuntimeException
{
}