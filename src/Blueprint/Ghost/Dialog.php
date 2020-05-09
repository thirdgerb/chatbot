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

use Commune\Blueprint\Ghost\Tools\Hearing;
use Commune\Blueprint\Ghost\Tools\Matcher;
use Commune\Blueprint\Ghost\Tools\Navigator;
use Commune\Blueprint\Ghost\Tools\DialogIoC;
use Commune\Blueprint\Ghost\Tools\Typer;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read Cloner $cloner
 * @property-read Context $context
 * @property-read Ucl $ucl              当前 Dialog 的 Context 地址.
 * @property-read Dialog|null $prev     前一个 Dialog
 */
interface Dialog
{

    /*----- conversation -----*/

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
     * @return Navigator
     */
    public function then() : Navigator;

    /**
     * @return Hearing
     */
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

    /*----- call -----*/

    /**
     * 上下文相关的 IoC 容器.
     * @return DialogIoC
     */
    public function ioc() : DialogIoC;

    /*----- 下一帧 -----*/

    /**
     * Dialog 逻辑运行一帧.
     * @return Dialog
     */
    public function tick() : Dialog;

    /**
     * Dialog 链当前的深度.
     * @return int
     */
    public function depth() : int;

    /**
     * @param Dialog $prev
     * @return static
     */
    public function withPrev(Dialog $prev) : Dialog;

    /*----- dialog status -----*/

    const ACTIVATE  = 1 << 1;
    const RETAIN    = 1 << 2;
    const WITHDRAW  = 1 << 3;
    const FINALE    = 1 << 4;
    const TEMPORARY = 1 << 5;

    // activate
    const REDIRECT_TO   = self::ACTIVATE | 1 << 8;
    const BLOCK_TO      = self::ACTIVATE | 1 << 9;
    const DEPEND_ON     = self::ACTIVATE | 1 << 10;
    const SLEEP_TO      = self::ACTIVATE | 1 << 11;
    const INTEND        = self::ACTIVATE | 1 << 12;
    const STAGING       = self::ACTIVATE | 1 << 13;
    const BACK_STEP     = self::ACTIVATE | 1 << 14;
    const HOME          = self::ACTIVATE | 1 << 15;

    // retain
    const FALLBACK      = self::RETAIN | 1 << 17;
    const HEED          = self::RETAIN | 1 << 18;
    const PREEMPT       = self::RETAIN | 1 << 19;
    const RESTORE       = self::RETAIN | 1 << 20;
    const WAKE          = self::RETAIN | 1 << 21;
    const WATCH         = self::RETAIN | 1 << 22;

    // withdraw
    const CONFUSE       = self::WITHDRAW | 1 << 25;
    const CANCEL        = self::WITHDRAW | 1 << 26;
    const REJECT        = self::WITHDRAW | 1 << 27;
    const QUIT          = self::WITHDRAW | 1 << 28;

    public function isStatus(int $statusType) : bool ;

}