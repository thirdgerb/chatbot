<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Operate;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Staging
{

    /**
     * 重置当前 context 的 stage 路径.
     * @return Redirect
     */
    public function clearPath() : Redirect;

    /**
     * 经过若干 stage, 然后回到当前节点.
     *
     * @param string $stageName
     * @param string ...$stageNames
     * @return Operator
     */
    public function circle(string $stageName, string ...$stageNames) : Operator;

    /**
     * 重启当前 Context, 但保留参数
     * @return Operator
     */
    public function restart() : Operator;

    /**
     * 重新激活当前 Stage
     * @return Operator
     */
    public function reactivate() : Operator;

    /**
     * 重置当前 Context 的数据. 并从头开始.
     * @return Operator
     */
    public function reset() : Operator;

}