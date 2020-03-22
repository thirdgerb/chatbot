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


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface DialogRouter
{

    /**
     * 等待用户接下来的消息
     *
     * @param array $suggestions
     * @param array $hearingStages
     * @param array $hearingContexts
     * @return Route
     */
    public function wait(
        array $suggestions,
        array $hearingStages,
        array $hearingContexts
    ) : Route;

    /**
     * @return Route
     */
    public function sleepTo(


    ) : Route;

    public function dependOn() : Route;

    public function yieldTo() : Route;


    public function block() : Route;

    /**
     * @return Route
     */
    public function quit() : Route;


    /**
     * 重置到上一次对话的状态
     * @return Route
     */
    public function rewind() : Route;

    /**
     * 重置到上上次对话的状态.
     *
     * @return Route
     */
    public function backStep() : Route;




}