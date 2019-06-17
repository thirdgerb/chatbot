<?php

/**
 * Class ConfigureException
 * @package Commune\Chatbot\Framework\Exceptions
 */

namespace Commune\Chatbot\Framework\Exceptions;


use Commune\Chatbot\Blueprint\Exceptions\StopServiceExceptionInterface;

/**
 * Class ConfigureException
 * @package Commune\Chatbot\Framework\Exceptions
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * 系统的配置错误导致的异常. 会终结系统, 不应该存在.
 */
class ConfigureException extends \LogicException implements StopServiceExceptionInterface
{
    /**
     * ConfigureException constructor.
     * @param string $message
     * @param \Throwable|null $e
     */
    public function __construct(string $message, \Throwable $e = null)
    {
        parent::__construct($message, 255, $e);
    }
}