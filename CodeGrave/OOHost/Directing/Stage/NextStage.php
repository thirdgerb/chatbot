<?php


namespace Commune\Chatbot\OOHost\Directing\Stage;


use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Backward\Fulfill;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 执行管道中的下一个stage. 如果没有的话, 则fulfill 当前的context
 */
class NextStage extends AbsNavigator
{
    public function doDisplay(): ? Navigator
    {
        $history = $this->history->nextStage();
        if (isset($history)) {
            return $this->startCurrent();
        }
        return new Fulfill($this->dialog);
    }

}