<?php

/**
 * Class RuntimeExceptionInterface
 * @package Commune\Chatbot\Blueprint\Exceptions
 */

namespace Commune\Chatbot\Blueprint\Exceptions;


/**
 * Interface RuntimeExceptionInterface
 * @package Commune\Chatbot\Blueprint\Exceptions
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * chatbot 运行过程中遭遇的致命异常. 通常会导致终结当前的客户端.
 */
interface RuntimeExceptionInterface
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