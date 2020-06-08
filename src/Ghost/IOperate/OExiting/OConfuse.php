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
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\IOperate\AbsOperator;
use Commune\Message\Host\SystemInt\DialogConfuseInt;
use Commune\Protocals\Comprehension;
use Commune\Protocals\HostMsg\Convo\EventMsg;

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

    public function __construct(Dialog $dialog, bool $silent)
    {
        $this->cloner = $dialog->cloner;
        $this->silent = $silent;
        parent::__construct($dialog);
    }

    protected function toNext(): Operator
    {
        $process = $this->dialog->process;

            // nlu 是否传入了回答
        $next = $this->hasMiddlewareReplies()
            // 是否是事件类消息. 事件类消息不触发.
            ?? $this->ifEventMsg()
            // 是否有可以 wake 的路由
            ?? $this->tryToWakeSleeping($process)
            // 是否有可以 restore 的路由
            ?? $this->tryToRestoreDying($process)
            // 是否有默认的理解程序
            ?? $this->tryDefaultConfuseAction()
            // 默认的回复.
            ?? $this->reallyConfuse();

        return $next;
    }

    protected function hasMiddlewareReplies() : ? Operator
    {
        $replies = $this
            ->cloner
            ->input
            ->comprehension
            ->replies
            ->getReplies();

        if (!empty($replies)) {
            $deliver = $this->dialog->send();
            foreach ($replies as $reply) {
                $deliver->message($reply);
            }
            return $deliver->over()->await();
        }

        return null;
    }

    protected function tryDefaultConfuseAction() : ? Operator
    {
        $action = $this->cloner->config->confuseHandler;
        if (isset($action)) {
            return $this->dialog->ioc()->call($action);
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

        if ($message instanceof EventMsg) {
            return $this->dialog->rewind($this->silent);
        }

        return null;
    }

    protected function reallyConfuse() : Operator
    {
        $uclStr = $this->dialog->ucl->encode();
        $matched = $this
                ->cloner
                ->input
                ->comprehension
                ->intention
                ->getMatchedIntent() ?? '';

        $this->dialog
            ->send()
            ->message(new DialogConfuseInt($uclStr, $matched));

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