<?php

/**
 * Class StopServiceExceptionInterface
 * @package Commune\Chatbot\Blueprint\Exceptions
 */

namespace Commune\Chatbot\Blueprint\Exceptions;


/**
 * Interface StopServiceExceptionInterface
 * @package Commune\Chatbot\Blueprint\Exceptions
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * 非常严重的异常, 需要彻底中断 chatbot app.
 */
interface StopServiceExceptionInterface
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