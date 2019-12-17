<?php

/**
 * Class QuitSession
 * @package Commune\Chatbot\OOHost\Directing\End
 */

namespace Commune\Chatbot\OOHost\Directing\End;


use Commune\Chatbot\OOHost\Dialogue\SubDialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 不可捕获, 直接退出 Session
 */
class QuitSession extends EndNavigator
{
    public function doDisplay(): ? Navigator
    {
        if ($this->dialog instanceof SubDialog) {
            // 只有 sub dialog 需要refresh.
            $this->dialog->history->refresh();
            $navigator = $this->dialog->fireQuit();
        }

        if (isset($navigator)) {
            return $navigator;
        }

        // 如果父级 dialog 不管, 子级就可以 quitSession
        $this->dialog
            ->session
            ->shouldQuit();

        return null;
    }

    public function beingHeard(): bool
    {
        return true;
    }


}