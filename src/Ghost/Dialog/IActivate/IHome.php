<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog\IActivate;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Activate\Home;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsDialogue;
use Commune\Ghost\Dialog\DialogHelper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IHome extends AbsDialogue implements Home
{
    protected $replace = false;

    public function __construct(Cloner $cloner, Ucl $homeUcl = null)
    {
        if (isset($homeUcl)) {
            $this->replace = true;
            $ucl = $homeUcl;
        } else {
            $process = $cloner->runtime->getCurrentProcess();
            $ucl = $process->decodeUcl($process->root);
        }

        parent::__construct($cloner, $ucl);
    }


    protected function runInterception(): ? Dialog
    {
        return null;
    }

    protected function runTillNext(): Dialog
    {
        // 直接运行.
        return DialogHelper::activate($this);
    }

    protected function selfActivate(): void
    {
        // 清空所有的 waiting.
        $process = $this->getProcess();
        $process->flushWaiting();
        // 重置
        if ($this->replace) {
            $process->replaceRoot($this->ucl);
        }
    }


}