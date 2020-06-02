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
use Commune\Framework\Spy\SpyAgency;
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
     * ITrace constructor.
     * @param int $maxTimes
     */
    public function __construct(int $maxTimes)
    {
        $this->maxTimes = $maxTimes;
        $this->debug = CommuneEnv::isDebug();
        SpyAgency::incr(static::class);
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

    public function log(LoggerInterface $logger): void
    {
        $logger->debug(
            "runtime trace",
            [
                'operates' => $this->operates,
            ]
        );
    }

    public function __destruct()
    {
        SpyAgency::decr(static::class);
    }

}