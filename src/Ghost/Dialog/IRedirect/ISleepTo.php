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
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsDialogue;
use Commune\Ghost\Dialog\DialogHelper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ISleepTo extends AbsDialogue implements Dialog\Activate\SleepTo
{
    /**
     * @var bool
     */
    protected $fallback;

    /**
     * @var string[]
     */
    protected $wakenStages;


    public function __construct(Dialog $prev, array $wakenStages, Ucl $to = null)
    {
        $this->prev = $prev;
        $this->fallback = !isset($to);
        $this->wakenStages = $wakenStages;
        $ucl = $to ?? $prev->ucl;

        parent::__construct($prev->cloner, $ucl);
    }

    protected function runInterception(): ? Dialog
    {
        return $this->fallback
            ? null
            : DialogHelper::intercept($this);
    }

    protected function runTillNext(): Dialog
    {
        if ($this->fallback) {
            $process = $this->getProcess();
            $process->unsetWaiting($this->ucl); // 先解决 current ucl.
            $from = $this;

            return $this->fallbackFlow($from, $process);
        }

        /**
         * @var Dialog
         */
        $dialog = DialogHelper::activate($this);
        return $dialog->withPrev($this->prev);
    }

    protected function selfActivate(): void
    {
        // 添加 waken
        $process = $this->getProcess();
        $process->addSleeping($this->prev->ucl, $this->wakenStages);
    }


}