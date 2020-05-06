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
use Commune\Blueprint\Ghost\Operator\Await;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsDialogue;
use Commune\Ghost\Runtime\IWaiter;
use Commune\Protocals\Host\Convo\QuestionMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IAwait extends AbsDialogue implements Await
{
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
    }

    public function askChoose(
        string $query,
        array $suggestions,
        $defaultChoice = null,
        bool $withRoutes = true
    ): Operator
    {
        // TODO: Implement askChoose() method.
    }

    public function askConfirm(string $query, bool $default = true): Operator
    {
        // TODO: Implement askConfirm() method.
    }

    public function askEntity(
        string $query,
        string $entityName
    ): Operator
    {
        // TODO: Implement askEntity() method.
    }

    public function askAny(
        string $query,
        array $suggestions = []
    ): Operator
    {
        // TODO: Implement askAny() method.
    }

    public function askMessage(string $protocal): Operator
    {
        // TODO: Implement askMessage() method.
    }

    public function askLoop(
        string $query,
        int $maxTurn
    ): Operator
    {
        // TODO: Implement askLoop() method.
    }


}