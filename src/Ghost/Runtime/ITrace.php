<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Runtime;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Exceptions\TooManyRedirectsException;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Runtime\Trace;
use Commune\Support\Arr\ArrayAbleToJson;
use Psr\Log\LoggerInterface;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ITrace implements Trace
{
    use ArrayAbleToJson;

    protected $records = [];

    protected $times = 0;

    /**
     * @var int
     */
    protected $maxTimes;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ITrace constructor.
     * @param int $maxTimes
     * @param bool $debug
     * @param LoggerInterface $logger
     */
    public function __construct(int $maxTimes, LoggerInterface $logger, bool $debug = true)
    {
        $this->maxTimes = $maxTimes;
        $this->debug = $debug;
        $this->logger = $logger;
    }


    public function toArray(): array
    {
        return $this->records;
    }

    public function record(Operator $operator): void
    {
        $this->times++;

        if ($this->times > $this->maxTimes) {
            throw new TooManyRedirectsException($this->times);
        }

        $this->records[] = $operator->getOperatorDesc();
    }

    public function __destruct()
    {
        if ($this->debug) {
            $this->logger->debug('runtime dialog trace : '. $this->toJson());
        }
    }


}