<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost;

use Commune\Blueprint\Ghost\Routing\Hearing;
use Commune\Blueprint\Ghost\Routing\Matcher;
use Commune\Blueprint\Ghost\Routing\Redirector;
use Commune\Blueprint\Ghost\Runtime\Task;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read Cloner $cloner
 * @property-read Context $context
 * @property-read Ucl $ucl              当前 Dialog 的 Context 地址.
 * @property-read Dialog|null $prev     前一个 Dialog
 * @property-read int $depth            当前 Dialog 的深度. 过深意味着循环重定向.
 */
interface Dialog
{

    /**
     * 发送消息给用户
     * @return Typer
     */
    public function send() : Typer;

    /**
     * 匹配工具.
     * @return Matcher
     */
    public function matcher() : Matcher;

    /**
     * 重定向当前的会话.
     *
     * @return Redirector
     */
    public function then() : Redirector;

    public function hearing() : Hearing;


    /*----- 上下文相关 Context -----*/

    /**
     * 在当前的上下文中创建一个 Context
     *
     * @param Ucl $ucl
     * @return Context
     */
    public function getContext(Ucl $ucl) : Context;

    /**
     * @param string $contextOrUclStr
     * @param array|null $query
     * @return Ucl
     */
    public function getUcl(string $contextOrUclStr, array $query = []) : Ucl;

    /**
     * Dialog 逻辑运行一帧.
     * @return Dialog
     */
    public function tick() : Dialog;

}