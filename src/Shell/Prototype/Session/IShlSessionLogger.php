<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Prototype\Session;

use Commune\Shell\Blueprint\Session\ShlSessionLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShlSessionLogger implements ShlSessionLogger
{
    use LoggerTrait;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $requestContext;

    /**
     * IShlSessionLogger constructor.
     * @param LoggerInterface $logger
     * @param array $context
     */
    public function __construct(LoggerInterface $logger, array $context)
    {
        $this->logger = $logger;
        $this->requestContext = $context;
    }

    public function log($level, $message, array $context = array())
    {
        $this->logger->log($level, $message, $context + $this->requestContext);
    }


}