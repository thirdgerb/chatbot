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
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Runtime\Process;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ORewind extends AbsFinale
{

    /**
     * @var bool
     */
    protected $silent;

    public function __construct(Dialog $dialog, bool $silent)
    {
        $this->silent = $silent;
        parent::__construct($dialog);
    }

    protected function toNext(): Operator
    {
        $process = $this->dialog->process;
        $prev = $process->prev;

        if (isset($prev)) {
            $this->setProcess($prev);
        }

        $this->runAwait($this->silent);
        return $this;
    }

}