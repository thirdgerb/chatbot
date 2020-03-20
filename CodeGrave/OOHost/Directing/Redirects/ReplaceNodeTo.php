<?php


namespace Commune\Chatbot\OOHost\Directing\Redirects;


use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 将当前 thread 正在运行的 context, 替换为另一个.
 * 由于会改变 intended 回调时的值, 所以替换的context 应该和当前context 是同样的类.
 */
class ReplaceNodeTo extends AbsRedirector
{
    public function doDisplay(): ? Navigator
    {
        $this->history->replaceNodeTo($this->to);
        return $this->startCurrent();
    }
}