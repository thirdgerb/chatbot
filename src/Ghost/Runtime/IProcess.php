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

use Commune\Blueprint\Ghost\Runtime\Node;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Runtime\Thread;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $belongsTo         进程所属的父 ID. 默认是 SessionId
 * @property-read string $id                进程的唯一 ID. 每一轮请求的 ID 都不一样.
 * @property-read string $prevId            上一个 Process 进程的 ID
 * @property-read string[] $sleeping        sleeping Thread 的 id
 * @property-read string[] $blocking        blocking Thread 的 id
 * @property-read string[] $gc              gc 中的 thread 的 id
 * @property-read string[] $backtrace
 * @property-read Node $root                root Node
 */
class IProcess implements Process, HasIdGenerator
{
    use IdGeneratorHelper, ArrayAbleToJson;

    /*----- 外部属性 -----*/

    /**
     * 通常是 cloneId
     * @var string
     */
    protected $belongsToId;

    /**
     * 进程 ID
     * @var string
     */
    protected $processId;


    /*----- 内部节点 -----*/

    /**
     * @var Node
     */
    protected $rootNode;

    /**
     * @var string
     */
    protected $aliveThreadId;

    /**
     * @var Thread[]    [threadId => Thread]
     */
    protected $threads = [];

    /**
     * @var int[]       [threadId => gcTurn]
     */
    protected $gcStack = [];

    /**
     * @var string[]    [threadId => 0]
     */
    protected $sleepingStack = [];

    /**
     * @var int[]       [threadId => priority]
     */
    protected $blockingStack = [];

    /*-------- prev ---------*/

    /**
     * @var Process|null
     */
    protected $prev;

    /**
     * @var string|null
     */
    protected $prevProcessId;

    /**
     * @var string[]    processIds
     */
    protected $backtraceIds = [];


    /*-------- expire ---------*/

    /**
     * @var bool
     */
    protected $shouldSave = false;

    public function __construct(
        string $belongsTo,
        Node $root,
        string $processId = null
    )
    {
        $this->belongsToId = $belongsTo;

        // 根节点
        $this->rootNode = $root;

        $this->processId = $processId ?? $this->createUuId();
        // 新的 Thread
        $rootThread = $root->toThread();
        $rootThreadId = $rootThread->id;
        $this->threads[$rootThreadId] = $rootThread;

        $this->aliveThreadId = $rootThreadId;

        $this->shouldSave = true;
    }

    /*-------- home --------*/

    public function home(Node $node = null): void
    {
        if (isset($node)) {
            $this->rootNode = $node;
        }

        // 清空 sleeping 和 gc, 但保留 blocking
        foreach ($this->sleepingStack as $id => $val) {
            unset($this->threads[$id]);
        }
        $this->sleepingStack = [];

        foreach ($this->gcStack as $id => $val) {
            unset($this->threads[$id]);
        }
        $this->gcStack = [];

        // 从 root 节点生成一个新的 Thread.
        $thread = $this->rootNode->toThread();
        $id = $thread->id;
        $this->threads[$id] = $thread;
        $this->aliveThreadId = $id;
    }


    /*-------- snapshot --------*/

    public function nextSnapshot(string $processId = null): Process
    {
        $processId = $processId ?? $this->createUuId();

        /**
         * @var IProcess $process
         */
        $process = clone $this;
        $process->processId = $processId;
        $process->prev = $this;
        $process->prevProcessId = $this->id;
        $process->shouldSave = true;
        array_unshift($process->backtraceIds, $this->prevProcessId);
        return $process;

    }

    /*-------- sleeping --------*/

    /**
     * @param Thread $thread
     * @param bool $top             放在栈顶, 还是栈尾
     */
    public function addSleepingThread(Thread $thread, bool $top = true) : void
    {
        $id = $thread->id;
        $this->threads[$id] = $thread;

        if ($top) {
            array_unshift($this->sleepingStack, $id);
        } else {
            $this->sleepingStack[] = $id;
        }
    }

    public function popSleeping(string $threadId = null) : ? Thread
    {
        if (!isset($threadId)) {
            foreach($this->sleepingStack as $key => $val) {
                $threadId = $key;
                break;
            }
        }

        if (is_null($threadId)) {
            return null;
        }

        // 如果 thread 是存在的.
        $thread = $this->threads[$threadId] ?? null;
        if (isset($thread)) {
            unset($this->sleepingStack[$threadId]);
            unset($this->threads[$threadId]);
            return $thread;
        }

        return null;
    }

    /*-------- blocking --------*/

    public function blockThread(Thread $thread): void
    {
        $id = $thread->id;
        $this->threads[$id] = $thread;
        $this->blockingStack[$id] = $thread->priority;
        rsort($this->blockingStack);
    }

    public function hasBlocking(): bool
    {
        return !empty($this->blockingStack);
    }

    public function popBlocking(): ? Thread
    {
        $threadId = array_shift($this->blockingStack);
        if (empty($threadId)) {
            return null;
        }
        $thread = $this->threads[$threadId];
        unset($this->threads[$threadId]);
        return $thread;
    }


    /*-------- alive --------*/

    public function aliveThread(): Thread
    {
        return $this->getThread($this->aliveThreadId);
    }

    public function aliveStageFullname(): string
    {
        return $this->aliveThread()->currentNode()->getStageFullname();
    }

    public function replaceAliveThread(Thread $thread): Thread
    {
        $aliveThread = $this->aliveThread();
        unset($this->threads[$this->aliveThreadId]);
        $newId = $thread->id;
        $this->threads[$newId] = $thread;
        $this->aliveThreadId = $newId;
        return $aliveThread;
    }

    public function challengeAliveThread(): ? Thread
    {
        $challenger = $this->popBlocking();
        if (empty($challenger)) {
            return null;
        }
        $aliveThread = $this->aliveThread();

        if ($challenger->priority > $aliveThread->priority) {
            $loser = $this->replaceAliveThread($challenger);
            return $loser;
        } else {
            $this->blockThread($challenger);
            return null;
        }
    }


    /*-------- thread --------*/

    public function hasThread(string $threadId): bool
    {
        return array_key_exists($threadId, $this->threads);
    }

    public function getThread(string $threadId): ? Thread
    {
        return $this->threads[$threadId] ?? null;
    }


    /*-------- gc thread --------*/

    public function addGcThread(Thread $thread, int $gcTurn): void
    {
        $id = $thread->id;
        $this->threads[$id] = $thread;
        $this->gcStack[$id] = $gcTurn;
        unset($this->blockingStack[$id]);
        unset($this->sleepingStack[$id]);
    }

    public function gcThreads(): void
    {
        $gcStack = [];
        foreach ($this->gcStack as $id => $turns) {
            $turns--;
            if ($turns > 0) {
                $gcStack[$id] = $turns;
            } else {
                unset($this->threads[$id]);
            }
        }

        $this->gcStack = $gcStack;
    }


    /*-------- getter --------*/

    public function __get($name)
    {
        switch ($name) {
            case 'belongsTo' :
                return $this->belongsToId;
            case 'id' :
                return $this->processId;
            case 'root' :
                return $this->rootNode;
            case 'sleeping':
                return array_keys($this->sleepingStack);
            case 'blocking':
                return array_keys($this->blockingStack);
            case 'gc' :
                return array_keys($this->gcStack);
            case 'prevId':
                return $this->prevProcessId;
            case 'backtrace':
                return $this->backtraceIds;
            default:
                return null;
        }
    }

    /*-------- cachable --------*/

    public function isCaching(): bool
    {
        // TODO: Implement isCaching() method.
    }

    public function expire(): void
    {
        // TODO: Implement expire() method.
    }

    public function getCachableId(): string
    {
        return $this->processId;
    }


    /*-------- clone --------*/

    public function __clone()
    {
        $this->rootNode = clone $this->rootNode;

        $threads = [];
        foreach ($this->threads as $id => $thread) {
            $threads[$id] = clone $thread;
        }

        $this->threads = $threads;
    }

    public function __sleep()
    {
        return [
            'belongsTo',
            'processId',
            'rootNode',
            'aliveThreadId',
            'threads',
            'gcThreads',
            'sleepingStack',
            'blockingStack',
            'gcStack',
            'prevProcessId',
            'backtraceIds',
        ];
    }

    public function __wakeup()
    {
        $this->shouldSave = false;
    }

    public function __destruct()
    {
        $this->threads = [];
        $this->gcStack = [];
        $this->sleepingStack = [];
        $this->blockingStack = [];
        $this->rootNode = null;
        $this->aliveThreadId = null;
    }
}
