<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog\Traits;

use Commune\Blueprint\Ghost\Runtime\Operator;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Ghost\Dialog\AbsDialog;
use Commune\Ghost\Dialog\IWithdraw\IQuit;
use Commune\Ghost\Runtime\Operators\CloseSession;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @mixin AbsDialog
 */
trait TQuitFlow
{

    public function quitWatching(Process $process) : ? Operator
    {
        $watchers = $process->getWatchers();

        foreach ($watchers as $watcher) {
            $quit = new IQuit($this->_cloner, $watcher, $this);
            $stageDef = $watcher->findStageDef($this->_cloner);
            $next = $stageDef->onWithdraw($quit);
            if (isset($next)) {
                return $next;
            }
        }

        return null;
    }

    public function quitSession() : Operator
    {
        return new CloseSession($this);
    }

}