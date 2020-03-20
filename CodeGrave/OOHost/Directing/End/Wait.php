<?php


namespace Commune\Chatbot\OOHost\Directing\End;

use Commune\Chatbot\OOHost\Dialogue\NeedDialogStatus;
use Commune\Chatbot\OOHost\Dialogue\SubDialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 等待用户发出信息.
 */
class Wait extends EndNavigator
{
    public function doDisplay(): ? Navigator
    {
        // 检查是否要记录会话的终态.
        $request = $this->dialog->session->conversation->getRequest();
        if ($request instanceof NeedDialogStatus) {
            $request->logDialogStatus($this->dialog);
        }

        // 触发嵌套会话的 wait 回调.
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