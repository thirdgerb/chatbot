<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IOperate\OFinale;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Await;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OAwait extends AbsFinale implements Await
{

    /**
     * @var array
     */
    protected $stageRoutes;

    /**
     * @var array
     */
    protected $contextRoutes;

    /**
     * @var int|null
     */
    protected $expire;

    /**
     * IAwait constructor.
     * @param Dialog $dialog
     * @param array $stageRoutes
     * @param array $contextRoutes
     * @param int $expire
     */
    public function __construct(
        Dialog $dialog,
        array $stageRoutes,
        array $contextRoutes,
        ? int $expire
    )
    {
        $this->dialog = $dialog;
        $this->stageRoutes = $stageRoutes;
        $this->contextRoutes = $contextRoutes;
        $this->expire = $expire;
        parent::__construct($dialog);
    }

    public function rewind(bool $dumb = false): Operator
    {
        return new ORewind($this->dialog, $dumb);
    }

    protected function toNext(): Operator
    {
        // TODO: Implement toNext() method.
    }

    public function backStep(int $step = 1): Operator
    {
        // TODO: Implement backStep() method.
    }

    public function askChoose(
        string $query,
        array $suggestions = [],
        $defaultChoice = 0,
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
        array $suggestions = [],
        string $messageType = VerbalMsg::class
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