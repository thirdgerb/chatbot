<?php


namespace Commune\Chatbot\OOHost\Directing\End;

use Commune\Chatbot\OOHost\Dialogue\SubDialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 等待用户发出信息.
 */
class Wait extends EndNavigator
{
    public function doDisplay(): ? Navigator
    {
        if ($this->dialog instanceof SubDialog) {
            return $this->dialog->fireWait();
        }
        return null;
    }

    public function beingHeard(): bool
    {
        return true;
    }


}