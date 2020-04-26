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

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Convo\ConvoStorage;
use Commune\Blueprint\Ghost\Memory\Recollection;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Runtime\Runtime;
use Commune\Contracts\Cache;
use Commune\Contracts\Ghost\RuntimeDriver;
use Commune\Support\RunningSpy\Spied;
use Commune\Support\RunningSpy\SpyTrait;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IRuntime implements Runtime, Spied
{
    use SpyTrait;

    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * @var RuntimeDriver
     */
    protected $driver;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var string
     */
    protected $traceId;


    /*---- cached ----*/

    /**
     * @var string
     */
    protected $currentProcessId;

    /**
     * @var array
     */
    protected $processes = [];

    /**
     * @var Recollection[]
     *
     *  [string $id => Recollection $recollection, ]
     */
    protected $recollections = [];

    /**
     * IRuntime constructor.
     * @param Cloner $cloner
     * @param RuntimeDriver $driver
     */
    public function __construct(Cloner $cloner, RuntimeDriver $driver)
    {
        $this->cloner = $cloner;
        $this->driver = $driver;
        $this->traceId = $cloner->getTraceId();
        $this->currentProcessId = $cloner->storage[ConvoStorage::CURRENT_PROCESS_ID] ?? '';

        static::addRunningTrace($this->traceId, $this->traceId);
    }

    /*---- processes ----*/

    public function getCurrentProcess(): Process
    {

    }

    public function setCurrentProcess(Process $process): void
    {
        // TODO: Implement setCurrentProcess() method.
    }

    public function createProcess(string $contextName): Process
    {
        // TODO: Implement createProcess() method.
    }

    public function findProcess(string $processId): ? Process
    {
        // TODO: Implement findProcess() method.
    }

    public function expireProcess(string $processId): void
    {
        // TODO: Implement expireProcess() method.
    }


    public function __destruct()
    {
        // 清空数组.
        $this->processes = [];

        static::removeRunningTrace($this->traceId);
    }
}