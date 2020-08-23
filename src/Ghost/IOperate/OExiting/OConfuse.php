<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IOperate\OExiting;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\MindDef\ContextStrategyOption;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\IOperate\AbsOperator;
use Commune\Message\Host\SystemInt\DialogConfuseInt;
use Commune\Protocals\HostMsg\Convo\EventMsg;
use Commune\Protocals\HostMsg\DefaultEvents;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OConfuse extends AbsOperator
{
    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * @var bool
     */
    protected $silent;

    protected $fallbackStrategy;

    /**
     * OConfuse constructor.
     * @param Dialog $dialog
     * @param bool $silent
     * @param callable|string|null $fallbackStrategy
     */
    public function __construct(
        Dialog $dialog,
        bool $silent,
        $fallbackStrategy = null
    )
    {
        $this->cloner = $dialog->cloner;
        $this->silent = $silent;
        $this->fallbackStrategy = $fallbackStrategy;
        parent::__construct($dialog);
    }

    protected function toNext(): Operator
    {
        $process = $this->dialog->process;

            // nlu 是否传入了回答
        $next = $this->hasComprehensionReplies()
            // 是否是事件类消息. 事件类消息不触发.
            ?? $this->ifEventMsg()
            // 是否有可以 wake 的路由
            ?? $this->tryToWakeSleeping($process)
            // 是否有可以 restore 的路由
            ?? $this->tryToRestoreDying($process)
            // 是否有默认的理解程序
            ?? $this->tryFallbackStrategy()
            // 默认的回复.
            ?? $this->reallyConfuse();

        return $next;
    }

    protected function hasComprehensionReplies() : ? Operator
    {
        $replies = $this
            ->cloner
            ->comprehension
            ->replies
            ->getReplies();

        if (!empty($replies)) {
            $deliver = $this->dialog->send();
            foreach ($replies as $reply) {
                $deliver->message($reply);
            }
            return $deliver->over()->rewind();
        }

        return null;
    }

    protected function tryFallbackStrategy() : ? Operator
    {
        if (isset($this->fallbackStrategy)) {
            $strategy = $this->fallbackStrategy;
            return $this->dialog->container()->call($strategy);
        }

        /**
         * @var ContextStrategyOption $contextStrategy
         */
        $contextStrategy = $this->dialog
            ->ucl
            ->findContextDef($this->cloner)
            ->getStrategy($this->dialog);

        $fallbackArr = $contextStrategy->heedFallbackStrategies;
        $fallbackArr = $fallbackArr ?? [];


        $defaultFallback = $this->cloner->config->defaultHeedFallback;
        if (!empty($defaultFallback)) {
            $fallbackArr[] = $defaultFallback;
        }

        if (empty($fallbackArr)) {
            return null;
        }

        foreach ($fallbackArr as $fallback) {
            $operator = $this->dialog->container()->call($fallback);
            if (isset($operator)) {
                return $operator;
            }
        }

        return null;
    }

    /**
     * 事件类消息不需要专门响应.
     * @return Operator|null
     */
    protected function ifEventMsg() : ? Operator
    {
        $message = $this->cloner->input->getMessage();

        if (!$message instanceof EventMsg) {
            return null;
        }

        if ($message->getEventName() === DefaultEvents::EVENT_CLIENT_CONNECTION) {
            return $this->dialog->rewind();
        } else {
            return $this->dialog->dumb();
        }
    }

    protected function reallyConfuse() : Operator
    {
        $uclStr = $this->dialog->ucl->encode();
        $matched = $this
                ->cloner
                ->comprehension
                ->intention
                ->getMatchedIntent() ?? '';

        $this->dialog
            ->send()
            ->message(DialogConfuseInt::instance($uclStr, $matched));

        return $this->dialog->rewind();
    }

    protected function tryToWakeSleeping(Process $process) : ? Operator
    {
        foreach ($process->sleeping as $id => $stages) {
            // empty
            if (empty($stages)) {
                continue;
            }

            $sleepingUcl = $process->getContextUcl($id);
            $matched = $this->matchStageRoutes($sleepingUcl, $stages);
            if (isset($matched)) {
                return $this->dialog->redirectTo($matched);
            }
        }
        return null;
    }

    protected function tryToRestoreDying(Process $process) : ? Operator
    {
        foreach ($process->dying as $id => list($turns, $stages)) {
            if (empty($stages)) {
                continue;
            }

            $dyingUcl = $process->getContextUcl($id);
            $matched = $this->matchStageRoutes($dyingUcl, $stages);
            if (isset($matched)) {
                return $this->dialog->redirectTo($matched);
            }
        }

        return null;
    }


    protected function matchStageRoutes(Ucl $current, array $stages = []) : ? Ucl
    {
        $matcher = $this->cloner->matcher->refresh();
        foreach ($stages as $stage) {
            $intentName = $current->getStageFullname($stage);
            if ($matcher->matchStage($intentName)->truly()) {
                return $current->goStage($stage);
            }
        }

        return null;
    }

    protected function matchContextRoutes(Ucl ...$contexts) : ? Ucl
    {
        $matcher = $this->cloner->matcher->refresh();

        foreach ($contexts as $ucl) {
            // 这个 ucl 可能是假的, 用了通配符
            $intentName = $ucl->getStageFullname();
            if ($matcher->matchStage($intentName)->truly()) {
                // 这个 ucl 就是真的了.
                return $ucl->goStageByFullname($intentName);
            }
        }

        return null;
    }
}