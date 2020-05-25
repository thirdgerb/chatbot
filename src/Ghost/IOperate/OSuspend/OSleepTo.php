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

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\IOperate\AbsOperator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OSleepTo extends AbsOperator
{

    /**
     * @var Ucl
     */
    protected $target;

    /**
     * @var array
     */
    protected $wakenStages;

    public function __construct(Dialog $dialog, Ucl $target, array $wakenStages)
    {
        $this->target = $target;
        $this->wakenStages = $wakenStages;

        parent::__construct($dialog);
    }

    protected function toNext(): Operator
    {
        $this->dialog
            ->process
            ->addSleeping(
                $this->dialog->ucl,
                $this->wakenStages
            );

        return $this->dialog->redirectTo($this->target);
    }

}