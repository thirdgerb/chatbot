<?php


namespace Commune\Chatbot\Framework\Exceptions;

use Commune\Chatbot\Blueprint\Exceptions\RequestExceptionInterface;

/**
 * 在请求中发生的异常, 导致无法响应用户
 */
class RequestException extends LogicException implements RequestExceptionInterface
{
}