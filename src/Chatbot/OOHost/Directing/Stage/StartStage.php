<?php


namespace Commune\Chatbot\OOHost\Directing\Stage;

use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 运行当前的stage
 *
 * @deprecated
 */
class StartStage extends AbsNavigator
{
    public function doDisplay(): ? Navigator
    {
        return $this->startCurrent();
    }


}