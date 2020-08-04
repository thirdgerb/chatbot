<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Context;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\MindDef\StageDef;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Dialog $dialog
 * @property-read StageDef $def
 */
interface StageBuilder
{
    /**
     * @param  callable|string $caller
     * @return StageBuilder
     */
    public function always($caller) : StageBuilder;

    /**
     * @param callable|string $caller
     * @return StageBuilder
     */
    public function onRedirect($caller) : StageBuilder;

    /**
     * @param callable|string $caller
     * @return StageBuilder
     */
    public function onActivate($caller) : StageBuilder;

    /**
     * @param callable|string $caller
     * @return StageBuilder
     */
    public function onReceive($caller) : StageBuilder;

    /**
     * @param callable|string $caller
     * @return StageBuilder
     */
    public function onResume($caller) : StageBuilder;

    /**
     * 当命中了 Dialog 事件时触发.
     *
     * @param string $event
     * @param callable|string $caller
     * @return StageBuilder
     */
    public function onEvent(string $event, $caller) : StageBuilder;

    /**
     * 当不是以下 Dialog 事件时触发.
     * 不包括 Redirect 事件.
     *
     * @param $caller
     * @param string ...$events
     * @return StageBuilder
     */
    public function onEventExcept($caller, string ...$events) : StageBuilder;

    /**
     * 如果前面的事件没有命中, 则触发这个事件.
     * @param string|callable $caller
     * @return StageBuilder
     */
    public function otherwise($caller) : StageBuilder;

}