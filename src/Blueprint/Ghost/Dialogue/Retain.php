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
use Commune\Blueprint\Ghost\Dialogue\Activate\BackStep;
use Commune\Blueprint\Ghost\Dialogue\Finale\Dumb;
use Commune\Blueprint\Ghost\Dialogue\Finale\Rewind;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Retain extends Dialog
{
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


    public function rewind() : Rewind;

    public function dumb() : Dumb;

    public function backStep() : BackStep;

    public function cancel();

    public function quit();

    public function fulfill();

    /**
     * 无法理解当前对话.
     * @return Operator
     */
    public function confuse() : Operator;

    /**
     * 重新激活当前 Stage.
     * @return Operator
     */
    public function reactivate() : Operator;

}
