<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IOperate\ORedirect;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\IOperate\AbsOperator;
use Commune\Ghost\IOperate\Flows\FallbackFlow;
use Commune\Protocols\HostMsg\Convo\ContextMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OYieldTo extends AbsOperator
{

    /**
     * @var Context
     */
    protected $target;

    /**
     * @var Ucl null
     */
    protected $fallback;

    /**
     * @var string
     */
    protected $toSessionId;

    /**
     * @var string|null
     */
    protected $toConvoId;

    public function __construct(
        Dialog $dialog,
        string $sessionId,
        Context $target,
        Ucl $fallback = null,
        string $convoId = null
    )
    {
        $this->target = $target;
        $this->fallback = $fallback;
        $this->toSessionId = $sessionId;
        $this->toConvoId = $convoId;

        parent::__construct($dialog);
    }

    protected function toNext(): Operator
    {

        $process = $this->dialog->process;

        $process->addDepending(
            $this->dialog->ucl,
            $this->target->getId()
        );

        $process->addYielding($this->target->getUcl());

        $this->dialog
            ->cloner
            ->dispatcher
            ->yieldContext(
                $this->toSessionId,
                $this->target,
                ContextMsg::MODE_BLOCKING,
                $this->toConvoId ?? ''
            );

        if (isset($this->fallback)) {
            return $this->dialog->redirectTo($this->fallback);
        }

        return new FallbackFlow($this->dialog);
    }

}