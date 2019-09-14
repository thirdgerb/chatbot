<?php


namespace Commune\Chatbot\OOHost\Context\Memory;

use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Session\Scope;

interface Memory extends Context
{
    /**
     * 获取 memory 生效的作用域.
     *
     * @see Scope
     *
     * @return string[]
     */
    public function getScopingTypes() : array;

    /**
     * 给当前memory加锁
     * @param int $expire 自动过期的秒数.
     * @return bool
     */
    public function lock(int $expire = 1) : bool;

    /**
     * 主动解锁.
     */
    public function unlock() : void;

}