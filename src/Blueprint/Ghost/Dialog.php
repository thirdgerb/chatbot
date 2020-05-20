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

use Commune\Blueprint\Ghost\Dialog\Finale\Await;
use Commune\Blueprint\Ghost\Memory\Recollection;
use Commune\Blueprint\Ghost\Tools\Hearing;
use Commune\Blueprint\Ghost\Tools\Matcher;
use Commune\Blueprint\Ghost\Tools\Navigator;
use Commune\Blueprint\Ghost\Tools\Caller;
use Commune\Blueprint\Ghost\Tools\Deliver;

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

    /*----- call -----*/

    /**
     * 上下文相关的 IoC 容器.
     * @return Caller
     */
    public function caller() : Caller;


    /*----- conversation -----*/

    /**
     * 发送消息给用户
     * @return Deliver
     */
    public function send() : Deliver;

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
    public function nav() : Navigator;

    /**
     * 等待用户的回复.
     *
     * @param array $allowContexts
     * @param array $stageRoutes
     * @param int|null $expire
     * @return Await
     */
    public function await(
        array $allowContexts = [],
        array $stageRoutes = [],
        int $expire = null
    ) : Await;


    /**
     * @return Hearing
     */
    public function hearing() : Hearing;

    /*----- memory -----*/

    /**
     * 回忆.
     * @param string $name
     * @return Recollection
     */
    public function recall(string $name) : Recollection;

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

    /*----- dialog event -----*/

    const ANY           = Dialog::class;
    const ACTIVATE      = Dialog\Activate::class;
    const RETAIN        = Dialog\Retain::class;
    const WITHDRAW      = Dialog\Withdraw::class;
    const FINALE        = Dialog\Finale::class;
    const INTERCEPT     = Dialog\Intercept::class;

    // activate
    const STAGING       = Dialog\Activate\Staging::class;
    const REDIRECT      = Dialog\Activate\Redirect::class;
    const DEPENDED      = Dialog\Activate\Depended::class;
    const INTEND        = Dialog\Activate\Intend::class;
    const HOME          = Dialog\Activate\Home::class;
    const PREEMPT       = Dialog\Activate\Preempt::class;

    // retain
    const FALLBACK      = Dialog\Retain\Fallback::class;
    const HEED          = Dialog\Retain\Heed::class;
    const RESTORE       = Dialog\Retain\Restore::class;
    const WAKE          = Dialog\Retain\Wake::class;
    const WATCH         = Dialog\Retain\Watch::class;
    const FULFILL       = Dialog\Retain\Fulfill::class;

    // withdraw
    const CONFUSE       = Dialog\Withdraw\Confuse::class;
    const CANCEL        = Dialog\Withdraw\Cancel::class;
    const REJECT        = Dialog\Withdraw\Reject::class;
    const FAIL          = Dialog\Withdraw\Fail::class;
    const QUIT          = Dialog\Withdraw\Quit::class;

    public function isEvent(string $statusType) : bool ;

    public function __invoke() : Dialog;
}