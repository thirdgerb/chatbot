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

use Commune\Ghost\Blueprint\Runtime\Process;
use Commune\Ghost\Blueprint\Runtime\Thread;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IProcess implements Process, HasIdGenerator
{
    use IdGeneratorHelper;


    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $belongsTo;

    /**
     * @var string
     */
    protected $current;

    /**
     * @var Thread[]
     */
    protected $threads = [];

    /**
     *
     * @var string[]
     */
    protected $blocking = [];

    /**
     * @var string[]
     */
    protected $sleeping = [];

    /**
     * @var int[] string => int
     */
    protected $gc = [];

    /**
     * @var string[] processId
     */
    protected $backSteps = [];

    /**
     * 是否已经
     * @var bool
     */
    protected $blocked = false;

    /**
     * @var string
     */
    protected $root;

    /**
     * @var null|Process
     */
    protected $prev;

    /*----- 启动相关的状态字段 -----*/

    /**
     * @var bool
     */
    protected $shouldSave = false;

    protected $booted = false;


    public function __construct(string $belongsTo, Thread $thread)
    {
        $this->belongsTo = $belongsTo;
        $this->current = $thread->id;
        $this->threads[$this->current] = $thread;
        $this->root = $this->current;
        $this->shouldSave = true;
    }

    public function fallback() : bool
    {
        $current = array_shift($this->sleeping);

        // 没有任何目标节点了
        if (empty($current)) {
            return false;
        }
        array_unshift($this->sleeping, $current);
        $this->current = $current;
        $this->shouldSave = true;
        return true;
    }

    public function home() : void
    {
        $root = $this->getThread($this->root);
        while($id = array_shift($this->sleeping)) {
            unset($this->threads[$id]);
        }
        unset($this->threads[$this->current]);

        $this->threads[$this->root] = $root;
        $this->current = $this->root;
        $this->shouldSave = true;
        return;
    }

    public function getThread(string $threadId) : Thread
    {
        return $this->threads[$threadId] ?? null;
    }

    public function gc() : void
    {
        foreach ($this->sleeping as $id) {
            if ($id === $this->root) {
                continue;
            }

            $thread = $this->getThread($id);
            if ($thread->gc()) {
                unset($this->threads[$id]);
            }
        }
    }


    /*-------- snapshot 快照历史 --------*/

    public function prev(): ? Process
    {
        return $this->prev;
    }

    public function backStep(int $steps): ? Process
    {
        if ($steps > 0) {

            if (isset($this->prev)) {
                return $this->prev->backStep($steps - 1);
            }

            return $this;

        } else {
            return $this;
        }
    }

    public function stepDepth(): int
    {
        if (isset($this->prev)) {
            return 1 + $this->prev->stepDepth();
        }

        return 1;
    }


    public function expireStep(int $max): bool
    {
        if ($max > 0 ) {
            if (!isset($this->prev)) {
                return false;
            }

            return $this->expireStep($max - 1);
        }

        if (isset($this->prev)) {
            $this->prev = null;
            return true;
        }

        return false;
    }

    /*-------- getter --------*/

    public function __get($name)
    {
        switch ($name) {
            case 'prev' :
                return $this->prev;
            default :
                return null;
        }
    }

}