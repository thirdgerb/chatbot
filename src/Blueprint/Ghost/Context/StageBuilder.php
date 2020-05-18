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

use Commune\Blueprint\Ghost\MindDef\StageDef;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface StageBuilder
{
    /*----- basic -----*/

    public function onIntercept($builder) : StageBuilder;

    /**
     * @param callable|string $builder
     * @return StageBuilder
     */
    public function onActivate($builder) : StageBuilder;

    public function onRetain($builder) : StageBuilder;

    public function onWithdraw($builder) : StageBuilder;

    /*----- event -----*/

    public function onEvent(string $event, $builder) : StageBuilder;

    public function onHeed($builder) : StageBuilder;

    public function end($fallback = null) : StageDef;

}