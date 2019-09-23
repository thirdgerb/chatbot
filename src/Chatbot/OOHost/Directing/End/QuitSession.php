<?php

/**
 * Class QuitSession
 * @package Commune\Chatbot\OOHost\Directing\End
 */

namespace Commune\Chatbot\OOHost\Directing\End;


use Commune\Chatbot\OOHost\Dialogue\SubDialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class QuitSession extends EndNavigator
{
    public function doDisplay(): ? Navigator
    {
        // 不管怎样, snapshot先清除掉.
        $this->dialog
            ->session
            ->repo
            ->clearSnapshot($this->dialog->history->belongsTo);

        if ($this->dialog instanceof SubDialog) {
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