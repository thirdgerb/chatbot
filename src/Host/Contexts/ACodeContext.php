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

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Ucl;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ACodeContext implements Context
{
    const DESCRIPTION = '';

    abstract public static function ucl(array $query = []) : Ucl;

    abstract public function __on_start(StageBuilder $stage) : StageDef;
}