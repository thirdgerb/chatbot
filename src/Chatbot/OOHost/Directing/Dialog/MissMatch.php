<?php


namespace Commune\Chatbot\OOHost\Directing\Dialog;


use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 没有命中任何逻辑, 告诉用户无法理解信息.
 */
class MissMatch extends AbsNavigator
{
    public function doDisplay(): ? Navigator
    {
//        $missMatched = $this->dialog
//            ->session
//            ->chatbotConfig
//            ->defaultMessages
//            ->messageMissMatched;
//        $this->dialog->say()->warning($missMatched);

        return null;
    }
}