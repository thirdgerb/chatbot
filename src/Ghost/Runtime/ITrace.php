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

use Commune\Blueprint\CommuneEnv;
use Commune\Blueprint\Ghost\Exceptions\TooManyRedirectsException;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Runtime\Trace;
use Psr\Log\LoggerInterface;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ITrace implements Trace
{

    /**
     * @var array
     */
    protected $operates = [];

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var int
     */
    protected $times = 0;

    /**
     * @var int
     */
    protected $maxTimes;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ITrace constructor.
     * @param int $maxTimes
     * @param LoggerInterface $logger
     */
    public function __construct(int $maxTimes, LoggerInterface $logger)
    {
        $this->maxTimes = $maxTimes;
        $this->logger = $logger;
        $this->debug = CommuneEnv::isDebug();
    }


    public function toArray(): array
    {
        return $this->operates;
    }

    public function record(Operator $operator): void
    {
        $this->times++;

        if ($this->times > $this->maxTimes) {
            throw new TooManyRedirectsException($this->times);
        }

        if ($this->debug) {
            $this->operates[] = $operator->getName();
        }
    }

    public function __destruct()
    {
        if ($this->debug) {
            $this->logger->debug('runtime dialog trace', [
                'operates' => $this->operates,

            ]);
        }
        unset($this->logger);
    }


}