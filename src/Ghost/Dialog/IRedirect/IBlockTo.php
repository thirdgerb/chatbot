<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog\IRedirect;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Activate\BlockTo;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsDialogue;
use Commune\Ghost\Dialog\DialogHelper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IBlockTo extends AbsDialogue implements BlockTo
{
    public function __construct(Dialog $prev, Ucl $to)
    {
        $this->prev = $prev;
        parent::__construct($prev->cloner, $to);
    }

    protected function runInterception(): ? Dialog
    {
        return DialogHelper::intercept($this);
    }

    protected function runTillNext(): Dialog
    {
        return DialogHelper::activate($this);
    }

    protected function selfActivate(): void
    {
        $prev = $this->prev;
        $prevContext = $prev->context;

        // block
        $process = $this->getProcess();
        $process->addBlocking($prevContext->getUcl(), $prevContext->getPriority());
    }


}