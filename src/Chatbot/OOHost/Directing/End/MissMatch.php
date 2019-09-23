<?php


namespace Commune\Chatbot\OOHost\Directing\End;


use Commune\Chatbot\OOHost\Dialogue\SubDialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 没有命中任何逻辑, 告诉用户无法理解信息.
 */
class MissMatch extends EndNavigator
{
    public function doDisplay(): ? Navigator
    {
        if ($this->dialog instanceof SubDialog) {
            return $this->dialog->fireMiss();
        }
        return null;
    }

    public function beingHeard(): bool
    {
        return false;
    }


}