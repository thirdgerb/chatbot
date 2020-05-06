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
use Commune\Blueprint\Ghost\Dialog\Activate\Intend;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsDialogue;
use Commune\Ghost\Dialog\DialogHelper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IIntend extends AbsDialogue implements Intend
{
    /**
     * @var Ucl[]
     */
    protected $paths = [];

    /**
     * IIntend constructor.
     * @param Cloner $cloner
     * @param Ucl $ucl
     * @param Ucl[] $path
     */
    public function __construct(Cloner $cloner, Ucl $ucl, array $path = [])
    {
        $this->paths = $path;
        parent::__construct($cloner, $ucl);
    }

    protected function runInterception(): ? Dialog
    {
        $stageDef = $this->ucl->findStageDef($this->cloner);
        return $stageDef->onIntercept($this, $this->prev);
    }

    protected function runTillNext(): Dialog
    {
        return DialogHelper::activate($this, $this->ucl);
    }

    protected function selfActivate(): void
    {
        $process = $this->getProcess();
        if (!empty($this->paths)) {
            $process->addPath(...$this->paths);
        }
    }


}