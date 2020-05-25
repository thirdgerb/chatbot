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

use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OBackStep extends AbsFinale
{
    /**
     * @var int
     */
    protected $step;


    public function __construct(Dialog $dialog, int $step)
    {
        if ($step <= 0) {
            throw new InvalidArgumentException("back step should greater than 0, $step given");
        }

        $this->step = $step;

        parent::__construct($dialog);
    }


    protected function toNext(): Operator
    {
        $this->dialog->process->backStep($this->step);
        $this->runAwait(false);
        return $this;
    }

}