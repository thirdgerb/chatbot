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

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Dialog $dialog
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
     * @param string $event
     * @param callable|string $caller
     * @return StageBuilder
     */
    public function onEvent(string $event, $caller) : StageBuilder;


}