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
use Commune\Blueprint\Ghost\Operate\Operator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface StageBuilder
{
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
     * @param string $event
     * @param callable|string $caller
     * @return StageBuilder
     */
    public function onEvent(string $event, $caller) : StageBuilder;

    /**
     * @param string $stage
     * @return StageBuilder
     */
    public function onCancel(string $stage) : StageBuilder;

    /**
     * @param string $stage
     * @return StageBuilder
     */
    public function onQuit(string $stage) : StageBuilder;

    /*----- event -----*/

    /**
     * @param Dialog $dialog
     * @return Operator|null
     */
    public function fire(Dialog $dialog) : ? Operator;

}