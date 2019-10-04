<?php


namespace Commune\Chatbot\Blueprint\Exceptions;


/**
 * 请求内部发生的异常. 将导致单个请求失败.
 */
interface RequestExceptionInterface
{

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return string
     */
    public function __toString();
}