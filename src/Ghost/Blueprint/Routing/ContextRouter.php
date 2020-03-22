<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Routing;

use Commune\Ghost\Blueprint\Routing\Route;

/**
 * 管理 Context (语境) 内部的切换
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ContextRouter
{

    /**
     * 前进:
     *  StageDef::onIntended
     *  ->closeThread()
     *
     * @return Route
     */
    public function fulfill(
        int $gcTurns = 0
    ) : Route;

    /**
     * @return Route
     */
    public function cancel() : Route;

    /**
     * 重置当前 Context 数据, 并从头开始
     * @return Route
     */
    public function reset() : Route;

    /**
     * 当前 Context 从 start 开始
     * @return Route
     */
    public function restart() : Route;

    /**
     * 前进到当前 Context 内部的 stage
     *
     * 前进:
     *  StageDef::onStart
     * @return Route
     */
    public function goStage() : Route;

    /**
     * 经过当前 Context 内管道式的一组 stages
     *
     * 前进:
     *  StageDef::onStart
     * @return Route
     */
    public function goStagePipes() : Route;


    /**
     * 进入当前 Context 下一个预备进入的管道.
     * 如果管道不存在, 则调用 fulfill
     *
     * 前进:
     *  StageDef::onStart
     *  ->fulfill()
     * @return Route
     */
    public function next() : Route;


}