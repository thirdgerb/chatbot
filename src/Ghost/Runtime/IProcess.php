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

use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Runtime\Waiter;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Protocals\HostMsg\Convo\QuestionMsg;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Utils\ArrayUtils;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $belongsTo             进程所属的 Session
 * @property-read string $id                    进程的唯一 ID.
 *
 */
class IProcess implements Process, HasIdGenerator
{
    use ArrayAbleToJson, IdGeneratorHelper;

    /**
     * @var string
     */
    protected $_id;

    /**
     * @var string
     */
    protected $_belongsTo;

    /**
     * @var string
     */
    protected $_root;

    /**
     * @var Waiter[]
     */
    protected $_backtrace = [];

    /**
     * @var null|Waiter
     */
    protected $_waiter;

    /**
     * @var array[]
     * [ string $ucl, int $status, string[] $paths ]
     */
    protected $_contexts = [];

    /*----- waiting -----*/

    /**
     * @var string[]
     */
    protected $_depending = [];

    /**
     * @var int[]
     */
    protected $_callbacks = [];

    /**
     * @var int[]
     */
    protected $_blocking = [];

    /**
     * @var string[][]
     */
    protected $_sleeping = [];

    /**
     * @var array
     */
    protected $_yielding = [];

    /**
     * @var array[]
     */
    protected $_watching = [];

    /**
     * @var array[]
     */
    protected $_dying = [];

    /*----- cached -----*/

    /**
     * @var null|Process
     */
    protected $_prev;

    /**
     * @var string[]
     */
    protected $_canceling;


    /*----- config -----*/

    public static $maxBacktrace = 20;

    public static $maxSleeping = 20;

    public static $maxBlocking = 20;

    public static $maxDying = 20;

    /**
     * IProcess constructor.
     * @param string $belongsTo
     * @param Ucl $root
     * @param string|null $id
     */
    public function __construct(
        string $belongsTo,
        Ucl $root,
        string $id = null
    )
    {
        $this->_belongsTo = $belongsTo;
        $this->_id = $id ?? $this->createUuId();
        $this->_root = $root->toEncodedStr();
    }

    public function nextSnapshot(string $id, int $maxBacktrace): Process
    {
        $next = clone $this;
        $next->_id = $id ?? $this->createUuId();
        $next->_prev = $this;
        return $next;
    }

    /*-------- to array --------*/

    public function toArray(): array
    {
        // todo
    }

    /*-------- wait --------*/

    public function await(
        Ucl $ucl,
        ? QuestionMsg $question,
        array $stageRoutes,
        array $contextRoutes
    ): void
    {
        $waiter = new IWaiter(
            $ucl,
            $stageRoutes,
            $contextRoutes,
            $question
        );

        if (!isset($this->_waiter)) {
            $this->_waiter = $waiter;
            return;
        }

        // backtrace
        $last = $this->_waiter;
        $this->_waiter = $waiter;

        array_unshift($this->_backtrace, $last);
        ArrayUtils::slice($this->_backtrace, self::$maxBacktrace);
    }

    /*-------- wait --------*/



    /*-------- magic --------*/

    public function __get($name)
    {
        // TODO: Implement __get() method.
    }

    public function __isset($name)
    {
        // TODO: Implement __isset() method.
    }

    public function __sleep()
    {
        // TODO: Implement __sleep() method.
    }

    public function __wakeup()
    {
        // TODO: Implement __wakeup() method.
    }

    public function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

}