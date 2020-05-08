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

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsDialogue;
use Commune\Ghost\Dialog\IActivate\IRedirectTo;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IYieldTo extends AbsDialogue
{

    /**
     * @var string;
     */
    protected $shellName;

    /**
     * @var string
     */
    protected $guestId;

    /**
     * @var Ucl
     */
    protected $dependOn;

    /**
     * @var Ucl
     */
    protected $to;

    public function __construct(
        Cloner $cloner,
        Ucl $current,
        string $shellName,
        string $guestId,
        Ucl $dependOn,
        Ucl $to = null
    )
    {
        $this->shellName = $shellName;
        $this->guestId = $guestId;
        $this->dependOn = $dependOn;
        $this->to = $to;
        parent::__construct($cloner, $current);
    }

    protected function runInterception(): ? Dialog
    {
        return null;
    }

    protected function runTillNext(): Dialog
    {
        // 拦截回调的过程.
        if (isset($this->to)) {
            return new IRedirectTo($this->cloner, $this->to);
        }

        return $this->fallbackFlow($this, $this->getProcess());
    }

    protected function selfActivate(): void
    {
        $process = $this->getProcess();
        $process->addYielding($this->ucl, $this->dependOn->getContextId());

        $yieldMsg = ''; //todo
        $this->cloner->asyncInput($yieldMsg);
    }


}