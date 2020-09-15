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
     * @var float
     */
    protected $last = 0.0;

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
        if ($this->debug) {
            $this->last = microtime(true);
        }
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
            $this->addTrace($operator);
        }
    }

    protected function addTrace(Operator $operator) : void
    {
        // operates and gap
        $now = microtime(true);
        $gap = $now - $this->last;
        $gap = round($gap * 1000000);
        $this->last = $now;
        $name = $operator->getName();
        $ucl = $operator->getDialog()->ucl->encode();
        $this->operates[] = [ "passed $gap us", $name, $ucl];
    }


    public function log(LoggerInterface $logger): void
    {
        $logger->debug(
            "runtime trace : " . json_encode($this->operates, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
    }

    public function __destruct()
    {
        SpyAgency::decr(static::class);
    }

}