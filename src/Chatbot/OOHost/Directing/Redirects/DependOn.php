<?php


namespace Commune\Chatbot\OOHost\Directing\Redirects;


use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 当前context 依赖一个目标 context
 * 目标context fulfill 的话, 会作为参数回调
 * 如果目标context 发生异常 cancel, reject, fail 的话, 当前context 也会因此退出.
 */
class DependOn extends Redirector
{
    public function doDisplay(): ? Navigator
    {
        $this->history->dependOn($this->to);
        return $this->startCurrent();
    }


}