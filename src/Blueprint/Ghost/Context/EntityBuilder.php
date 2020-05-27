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

use Commune\Blueprint\Ghost\MindDef\ParamDefCollection;
use Commune\Blueprint\Ghost\MindDef\StageDef;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface EntityBuilder
{

    public function define(
        string $name,
        string $query,
        string $validator = null,
        string $type = null,
        string $parser = null
    );



    public function getEntityParams() : ParamDefCollection;

    /**
     * @return StageDef[]
     */
    public function getEntityStages() : array;
}