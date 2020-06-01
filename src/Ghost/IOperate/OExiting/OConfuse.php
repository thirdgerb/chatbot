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

        return $this->ifEventMsg()
            ?? $this->tryToWakeSleeping($process)
            ?? $this->tryToRestoreDying($process)
            ?? $this->tryConfuseAction()
            ?? $this->reallyConfuse();
    }

    protected function tryConfuseAction() : ? Operator
    {
        $action = $this->cloner->config->confuseHandler;
        if (isset($action)) {
            return $this->dialog->caller()->call($action);
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
        $uclStr = $this->dialog->ucl->toEncodedStr();
        $this->dialog
            ->send()
            ->message(new DialogConfuseInt($uclStr));

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