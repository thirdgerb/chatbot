<?php


namespace Commune\Chatbot\OOHost\Exceptions;

use Commune\Chatbot\Blueprint\Exceptions\CloseSessionException;

/**
 * 会话重连太多次, 终止会话.
 */
class TooManyRedirectException extends CloseSessionException
{
}