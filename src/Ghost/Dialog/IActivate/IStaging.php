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
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsDialogue;
use Commune\Ghost\Dialog\DialogHelper;
use Commune\Blueprint\Ghost\Dialog\Activate\Staging;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IStaging extends AbsDialogue implements Staging
{



    /**
     * @var Ucl[]
     */
    protected $stages = [];

    /**
     * IStaging constructor.
     * @param Cloner $cloner
     * @param Ucl $ucl
     * @param Ucl[] $stages
     */
    public function __construct(Cloner $cloner, Ucl $ucl, array $stages = [])
    {
        $this->stages = $stages;
        parent::__construct($cloner, $ucl);
    }

    protected function runInterception(): ? Dialog
    {
        // 不相同的 stage, 也会调用 onIntercept 方法.
        return DialogHelper::intercept($this);
    }

    protected function runTillNext(): Dialog
    {
        return DialogHelper::activate($this);
    }


    protected function selfActivate(): void
    {
        $process = $this->getProcess();
        $process->unsetWaiting($this->ucl->toEncodedStr());

        if (!empty($this->stages)) {
            $process->addPath(...$this->stages);
        }
    }



}