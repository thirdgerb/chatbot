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
    protected $gcThreads = [];

    /**
     * @var string[]    [threadId => 0]
     */
    protected $sleepingStack = [];

    /**
     * @var int[]       [threadId => priority]
     */
    protected $blockingStack = [];

    /**
     * @var Process|null
     */
    protected $prev;

    /**
     * @var string|null
     */
    protected $prevId;

    /**
     * @var bool
     */
    protected $shouldSave = false;

    /**
     * @var string[]    processIds
     */
    protected $backtraceIds = [];

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
    }




    /**
     * Clone 之后再调用这个方法.
     * @param Process $prev
     * @param string|null $processId
     * @return Process
     */
    public function fromPrev(Process $prev, string $processId = null) : Process
    {
        $this->processId = $processId ?? $this->createUuId();
        $this->prev = $prev;
        $this->prevId = $prev->id;
        array_unshift($this->backtraceIds, $prev->prevId);
        return $this;
    }

    /*-------- properties --------*/



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



    /*-------- alive --------*/

    public function aliveThread(): Thread
    {
        return $this->getThread($this->aliveThreadId);
    }

    public function aliveStageFullname(): string
    {
        return $this->aliveThread()->currentNode()->getStageFullname();
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
            default:
                return null;
        }
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
            'prevId',
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
        $this->gcThreads = [];
        $this->sleepingStack = [];
        $this->blockingStack = [];
        $this->rootNode = null;
        $this->aliveThreadId = null;
    }
}
