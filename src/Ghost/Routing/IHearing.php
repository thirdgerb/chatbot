<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Routing;

use Commune\Framework\Exceptions\InvalidCallableReturnException;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Routing\Hearing;
use Commune\Blueprint\Ghost\Stage\OnHeed;
use Commune\Ghost\GhostConfig;
use Commune\Message\Blueprint\EventMsg;
use Commune\Support\Utils\TypeUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IHearing implements Hearing
{
    /**
     * @var OnHeed
     */
    public $heed;

    /**
     * @var null|Operator
     */
    public $operator = null;

    /**
     * @var GhostConfig
     */
    protected $ghostConfig;

    /**
     * @var callable[]
     */
    protected $fallbackCallers = [];

    protected $calledFallback = false;

    protected $calledDefaultFallback = false;

    /**
     * IHearing constructor.
     * @param OnHeed $heed
     */
    public function __construct(OnHeed $heed)
    {
        $this->heed = $heed;
    }

    public function toDo(callable $operating, callable $when): Hearing
    {
        if (isset($this->operator)) {
            return $this;
        }

        $result = $this->heed->call($when);

        if (!is_bool($result)) {
            throw new InvalidCallableReturnException(
                __METHOD__,
                'bool',
                TypeUtils::getType($result)
            );
        }

        if (!$result) {
            return $this;
        }

        $this->callOperating(__METHOD__, $operating);
        return $this;
    }


    public function action(callable $operating): Hearing
    {
        if (isset($this->operator)) {
            return $this;
        }

        $this->callOperating(__METHOD__, $operating);
        return $this;
    }

    protected function callOperating(string $method, callable $operating) : bool
    {

        $operator = $this->heed->call($operating);

        if ((is_object($operator) && $operator instanceof Operator)) {
            $this->operator = $operator;
            return true;
        }

        // 抛出异常.
        if (!is_null($operator)) {
            throw new InvalidCallableReturnException(
                $method,
                Operator::class . ' or null',
                TypeUtils::getType($operator)
            );
        }

        return false;
    }

    public function setOperator(Operator $operator) : void
    {
        $this->operator = $operator;
    }

    public function always(callable $operating): Hearing
    {
        $this->callOperating(__METHOD__, $operating);
        return $this;
    }

    public function end(): Operator
    {
        if (isset($this->operator)) {
            return $this->operator;
        }

        $this->runFallback();
        $this->runDefaultFallback();

        return $this->heardOrConfuse();
    }

    public function privateEnd(): Operator
    {
        if (isset($this->operator)) {
            return $this->operator;
        }

        $this->runFallback();
        return $this->heardOrConfuse();
    }

    public function heardOrConfuse(): Operator
    {
        // 事件类消息不处理.
        if ($this->heed->matcher()->isInstanceOf(EventMsg::class)) {
            return $this->heed->dumb();
        }

        return $this->operator ?? $this->heed->confuse();
    }

    public function fallback(callable  ...$operating): Hearing
    {
        $this->fallbackCallers  = array_merge($this->fallbackCallers, $operating);
    }

    public function runFallback(): Hearing
    {
        if ($this->calledDefaultFallback || isset($this->operator)) {
            return $this;
        }

        if (empty($this->fallbackCallers)) {
            return $this;
        }

        foreach ($this->fallbackCallers as $caller) {
            if ($this->callOperating(__METHOD__, $caller)) {
                break;
            }
        }

        return $this;
    }

    public function runDefaultFallback(): Hearing
    {
        if ($this->calledDefaultFallback || isset($this->operator)) {
            return $this;
        }

        $callers = $this->getGhostConfig()->heedFallback;
        if (empty($callers)) {
            return $this;
        }

        foreach ($callers as $caller) {
            if ($this->callOperating(__METHOD__, $caller)) {
                break;
            }
        }

        return $this;
    }


    protected function getGhostConfig() : GhostConfig
    {
        return $this->ghostConfig
            ?? $this->ghostConfig = $this
                ->heed
                ->conversation
                ->container
                ->make(GhostConfig::class);
    }

    public function __destruct()
    {
        unset($this->fallbackCallers);
    }
}