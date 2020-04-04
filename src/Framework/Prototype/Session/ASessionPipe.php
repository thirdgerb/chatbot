<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Prototype\Session;

use Commune\Framework\Blueprint\Session\Session;
use Commune\Framework\Blueprint\Session\SessionPipe;
use Commune\Framework\Prototype\Session\Events\LeaveSessionPipe;
use Commune\Framework\Prototype\Session\Events\EnterSessionPipe;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ASessionPipe implements SessionPipe
{
    /**
     * @var bool
     */
    private static $debug = false;

    /**
     * @var string
     */
    private static $pipelineLogInfo;

    /**
     * @var float|null
     */
    protected $startAt;

    /**
     * @var bool
     */
    protected $propagation = true;

    /**
     * @var string
     */
    protected $via = SessionPipe::SYNC;

    /**
     * @param Session $session
     * @return Session
     */
    abstract protected function before($session);

    /**
     * @param Session $session
     * @return Session
     */
    abstract protected function after($session);

    public function stopPropagation(): void
    {
        $this->propagation = false;
    }


    public function sync(Session $session, callable $next): Session
    {
        $this->onEnter($session);
        $session = $this->before($session);

        // 执行下一步.
        $session = $this->next($session, $next);

        // 结束了就没有 after 环节了.
        if ($this->propagation && !$session->isFinished()) {
            $session = $this->after($session);
        }
        $this->onLeave($session);

        return $session;
    }

    public function isAsync(): bool
    {
        return $this->via !== SessionPipe::SYNC;
    }

    public function isAsyncInput(): bool
    {
        return $this->via === SessionPipe::ASYNC_INPUT;
    }

    public function isAsyncOutput(): bool
    {
        return $this->via === SessionPipe::ASYNC_OUTPUT;
    }


    public function asyncInput(Session $session, callable $next): Session
    {
        $this->via = SessionPipe::ASYNC_INPUT;
        $this->onEnter($session);
        $session = $this->before($session);
        $session = $this->next($session, $next);

        // 没有 after 环节.
        $this->onLeave($session);
        return $session;
    }

    public function asyncOutput(Session $session, callable $next): Session
    {
        $this->via = SessionPipe::ASYNC_OUTPUT;
        $this->onEnter($session);
        // 没有 before 环节.
        $session = $this->next($session, $next);
        $session = $this->after($session);
        $this->onLeave($session);
        return $session;
    }


    protected function next(Session $session, callable $next): Session
    {
        // 结束了就没有下一步了.
        if ($this->propagation && !$session->isFinished()) {
            $session = $next($session);
        }
        return $session;
    }


    protected function onEnter(Session $session) : void
    {
        $session->fire(new EnterSessionPipe($this));
        $debug = self::$debug
            ?? self::$debug = $session->getApp()->isDebugging();

        if ($debug) {
            $this->startAt = microtime(true);
        }
    }

    protected function onLeave(Session $session) : void
    {
        if (isset($this->start)) {
            $pipeName = static::class;
            // 记录时间.
            $end = microtime(true);
            $gap = abs(intval(($end - $this->start) * 1000));

            $logInfo = self::$pipelineLogInfo
                ?? self::$pipelineLogInfo = $session
                    ->getApp()
                    ->getLogInfo()
                    ->sessionPipelineLog();

            $session->getLogger()->info(
                $logInfo,
                [
                    'pipe' => $pipeName,
                    'gap' => $gap,
                    'mem' => memory_get_usage(true)
                ]
            );
        }

        $session->fire(new LeaveSessionPipe($this));
    }


}