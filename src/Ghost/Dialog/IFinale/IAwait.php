<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog\IFinale;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsBaseDialog;
use Commune\Ghost\Dialog\IStartProcess;
use Commune\Ghost\Runtime\IWaiter;
use Commune\Protocals\HostMsg\Convo\QuestionMsg;
use Commune\Blueprint\Ghost\Dialog\Finale\Await;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IAwait extends AbsBaseDialog implements Await
{
    const SELF_STATUS = self::FINALE;

    /**
     * @var string[]
     */
    protected $stageRoutes;

    /**
     * @var string[]
     */
    protected $contextRoutes;

    /**
     * @var int
     */
    protected $expire;

    /**
     * @var QuestionMsg|null
     */
    protected $question;

    /**
     * @var bool
     */
    protected $restartProcess = false;

    public function __construct(
        Cloner $cloner,
        Ucl $ucl,
        array $stageRoutes,
        array $contextRoutes,
        ?int $expire
    )
    {
        $this->stageRoutes = $stageRoutes;
        $this->contextRoutes = $contextRoutes;
        $this->expire = $expire;
        parent::__construct($cloner, $ucl);
    }

    protected function runInterception(): ? Dialog
    {
        return null;
    }

    protected function runTillNext(): Dialog
    {
        if ($this->restartProcess) {
            return new IStartProcess($this->cloner);
        }
        $this->ticked = true;
        return $this;
    }

    protected function selfActivate(): void
    {
        $process = $this->getProcess();
        $process->setAwait(new IWaiter(
            $this->ucl,
            $this->stageRoutes,
            array_map(function($route){
                return strval($route);
            }, $this->contextRoutes),
            $this->question
        ));

        // 设置 expire
        if (isset($this->expire)) {
            $this->cloner->setSessionExpire($this->expire);
        }

        // 尝试同步状态变更.
        $runtime = $this->cloner->runtime;
        $contextMsg = $runtime->toContextMsg();
        if (isset($contextMsg)) {
            $input = $this->cloner->input;
            $this->cloner->output($input->output($contextMsg));
        }
    }

    public function askChoose(
        string $query,
        array $suggestions,
        $defaultChoice = null,
        bool $withRoutes = true
    ): Await
    {
        // TODO: Implement askChoose() method.
    }

    public function askConfirm(string $query, bool $default = true): Await
    {
        // TODO: Implement askConfirm() method.
    }

    public function askEntity(
        string $query,
        string $entityName
    ): Await
    {
        // TODO: Implement askEntity() method.
    }

    public function askAny(
        string $query,
        array $suggestions = []
    ): Await
    {
        // TODO: Implement askAny() method.
    }

    public function askMessage(string $protocal): Await
    {
        // TODO: Implement askMessage() method.
    }

    public function askLoop(
        string $query,
        int $maxTurn
    ): Await
    {
        // TODO: Implement askLoop() method.
    }


    public function restartProcess(): Await
    {
        $this->restartProcess = true;
    }


}