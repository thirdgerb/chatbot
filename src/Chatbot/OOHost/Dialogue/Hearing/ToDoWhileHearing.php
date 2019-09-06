<?php


namespace Commune\Chatbot\OOHost\Dialogue\Hearing;

use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Dialogue\Hearing;


/**
 * to do 的方式定义 hear 的筛选规则.
 * 先确定能干什么, 然后再设置条件.
 * 在某些场景下, 能更清楚地看懂 hearing 能够响应的功能.
 *
 * use to do api to define hearing.
 * define action before conditions.
 * easier to see what actions can hearing api do.
 *
 * @see Hearing
 */
interface ToDoWhileHearing extends Matcher
{
    /**
     * 返回到 hearing 的语境
     * @return Hearing
     */
    public function otherwise() : Hearing;

    public function todo(callable $action) : ToDoWhileHearing;

    public function end(callable $fallback = null) : Navigator;

}