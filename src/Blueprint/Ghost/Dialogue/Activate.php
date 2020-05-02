<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Dialogue;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialogue\Activate\Depend;
use Commune\Blueprint\Ghost\Dialogue\Finale\Await;
use Commune\Blueprint\Ghost\Dialogue\Finale\Dumb;
use Commune\Blueprint\Ghost\Dialogue\Finale\Rewind;
use Commune\Blueprint\Ghost\Routing\Staging;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Activate extends
    Dialog
{
    /**
     * 等待用户的回复.
     *
     * @param array $stageRoutes
     * @param array $contextRoutes
     * @param int|null $expire
     * @return Await
     */
    public function await(
        array $stageRoutes = [],
        array $contextRoutes = [],
        int $expire = null
    ) : Await;

    /**
     * 从开头重新走 Context 的流程.
     *
     * @param bool $reset
     * @return Staging
     */
    public function restartContext(bool $reset = false) : Staging;

    /**
     * 沿着一个或者多个 Stage 的路径前进.
     * 会插入到当前管道的头部.
     *
     * 例如管道: A B C ; 调用 next(E, F, G); 结果 E F G A B C
     *
     * @param string[] ...$stageNames
     * @return Operator
     */
    public function nextStage(...$stageNames) : Staging;


    /**
     * 沿着多个 Stage 前进, 并且变更之前的 Stage
     *
     * @param string[] ...$stageNames
     * @return Operator
     */
    public function resetStages(...$stageNames) : Staging;

    public function depend() : Depend;

    public function sleepTo() : Dialog;

    public function yieldTo() : Dialog;

    public function replaceTo() : Dialog;

    public function reject() : Dialog;

    public function fulfill();

}