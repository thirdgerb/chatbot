<?php


namespace Commune\Chatbot\OOHost\Directing\Backward;


use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 重置 breakpoint, 从而使对话还原到上一轮结束时的状态.
 * 不过过程中发送的消息也都会正常发送.
 * 暂时没做反悔不发送消息的功能.
 */
class Rewind extends AbsNavigator
{
    public function doDisplay(): ? Navigator
    {
        $this->history->rewind();

        $question = $this->dialog->currentQuestion();
        if (isset($question)) {
            $this->dialog->reply($question);
        }

        return null;
    }


}