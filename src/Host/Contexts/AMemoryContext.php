<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Host\Contexts;

use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Host\Contexts\CodeContext\AsMemory;
use Commune\Host\Contexts\CodeContext\DefineParam;
use Commune\Host\Contexts\CodeContext\HasEntity;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AMemoryContext extends ACodeContext implements
    AsMemory,
    DefineParam,
    HasEntity
{
    public function __on_start(StageBuilder $stage): StageDef
    {
        return $stage
            ->onActivate(function(Dialog $dialog) {
                return $dialog->nav()->fulfill();
            })
            ->end();
    }


}