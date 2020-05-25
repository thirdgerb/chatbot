<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IOperate\OSuspend;

use Commune\Blueprint\Ghost\Ucl;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Ghost\IOperate\AbsOperator;
use Commune\Blueprint\Ghost\Operate\Operator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OBlockTo extends AbsOperator
{

    /**
     * @var Ucl
     */
    protected $target;

    /**
     * @var int
     */
    protected $priority;

    public function __construct(Dialog $dialog, Ucl $target, int $priority = null)
    {
        $this->target = $target;
        $this->priority = $priority ?? $target
                ->findContextDef($dialog->cloner)
                ->getPriority();

        parent::__construct($dialog);
    }

    protected function toNext(): Operator
    {
        $this->dialog
            ->process
            ->addBlocking(
                $this->dialog->ucl,
                $this->priority
            );

        return $this->dialog->redirectTo($this->target);
    }

}