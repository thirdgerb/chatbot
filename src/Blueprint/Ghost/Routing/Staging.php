<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Routing;

use Commune\Blueprint\Ghost\Operator\Operator;


/**
 * 同一个 Context 内部变更 Stage
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Staging
{
    /**
     * 从开头重新走 Context 的流程.
     *
     * @param bool $reset
     * @return Operator
     */
    public function restart(bool $reset = false) : Operator;

    /**
     * 沿着一个或者多个 Stage 的路径前进.
     * 会插入到当前管道的头部.
     *
     * 例如管道: A B C ; 调用 next(E, F, G); 结果 E F G A B C
     *
     * @param string[] ...$stageNames
     * @return Operator
     */
    public function next(...$stageNames) : Operator;


    /**
     * 沿着多个 Stage 前进, 并且变更之前的 Stage
     *
     * @param string[] ...$stageNames
     * @return Operator
     */
    public function swerve(...$stageNames) : Operator;

}