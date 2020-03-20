<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Router;

use Commune\Ghost\Blueprint\Redirector;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GoStage
{
    /**
     * 重复当前 Stage
     * @return Redirector
     */
    public function repeat() : Redirector;

    /**
     * 回到当前 Context 的起点
     * @return Redirector
     */
    public function restart() : Redirector;

    /**
     * 重置当前 Context, 包括重置 Context 的数据
     * @return Redirector
     */
    public function rewind() : Redirector;

    /**
     * 切换 Stage
     * @param string $stageName
     * @return Redirector
     */
    public function to(string $stageName) : Redirector;

    /**
     * 依次进入若干个 Stage
     * @param string[] $stageNames
     * @return Redirector
     */
    public function throughPipes(array $stageNames) : Redirector;

    /**
     * 重置当前的 Stage pipes
     */
    public function resetPipes() : void;

    /**
     * 进入下一个 Stage, 不存在的话则执行 fulfill
     * @return Redirector
     */
    public function next() : Redirector;


}