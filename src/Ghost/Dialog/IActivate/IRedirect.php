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
use Commune\Blueprint\Ghost\Dialog\Activate\Redirect;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IRedirect extends AbsDialogue implements Redirect
{
    const SELF_STATUS = self::REDIRECT_TO;

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
        return DialogHelper::intercept($this);
    }

    protected function runTillNext(): Dialog
    {
        return DialogHelper::activate($this);
    }

    protected function selfActivate(): void
    {
        $process = $this->getProcess();
        $process->unsetWaiting($this->ucl->toEncodedUcl());
        if (!empty($this->paths)) {
            $process->addPath(...$this->paths);
        }
    }


}