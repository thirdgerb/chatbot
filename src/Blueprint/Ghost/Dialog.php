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

    /*----- dialog event -----*/

    // 任意 dialog
    const ANY           = Dialog::class;
    // stage 启动时
    const ACTIVATE      = Dialog\Activate::class;
    // stage 回调时
    const RETAIN        = Dialog\Retain::class;
    // stage 退出时
    const WITHDRAW      = Dialog\Withdraw::class;
    // session 结束时
    const FINALE        = Dialog\Finale::class;
    // intercept
    const INTERCEPT     = Dialog\Intercept::class;

    /* activate 启动一个 stage */

    // 相同的 context 里一个 stage 进入另一个 stage
    const STAGING       = Dialog\Activate\Staging::class;
    // 一个 context 依赖到另一个 context 时
    const DEPENDED      = Dialog\Activate\Depend::class;
    // 从一个 context 因为意图进入到另一个 context 时
    const INTEND        = Dialog\Activate\Intend::class;
    // watch
    const WATCH         = Dialog\Activate\Watch::class;
    // 从一个 context 主动进入到另一个 context 里
    const REDIRECT      = Dialog\Activate\Redirect::class;
    // 返回到根路径.
    const HOME          = Dialog\Activate\Home::class;
    // 一个 blocking context 重新占据对话
    const PREEMPT       = Dialog\Activate\Preempt::class;
    // reactivate
    const REACTIVATE    = Dialog\Activate\Reactivate::class;

    /* retain 中断的 stage 得到回调 */

    // sleep -> wake
    const WAKE          = Dialog\Retain\Wake::class;
    // await -> heed
    const HEED          = Dialog\Retain\Heed::class;
    // dying -> restore
    const RESTORE       = Dialog\Retain\Restore::class;
    // depending -> fulfill
    const FULFILL       = Dialog\Retain\Fulfill::class;

    /* withdraw */

    // confuse : fallback -> sleeping, watch
    const CONFUSE       = Dialog\Withdraw\Confuse::class;
    // cancel : depending, blocking, fallback -> sleeping, watch
    const CANCEL        = Dialog\Withdraw\Cancel::class;
    // reject : depending, blocking, fallback -> sleeping, watch
    const REJECT        = Dialog\Withdraw\Reject::class;
    // fail : depending, blocking, fallback -> sleeping, watch
    const FAIL          = Dialog\Withdraw\Fail::class;
    // quit : depending, blocking, fallback -> sleeping, watch
    const QUIT          = Dialog\Withdraw\Quit::class;

    public function isEvent(string $statusType) : bool ;

    public function withPrev(Dialog $dialog) : Dialog;

    /**
     * 可以作为后续.
     * @return Dialog
     */
    public function __invoke() : Dialog;
}