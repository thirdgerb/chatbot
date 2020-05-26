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

/**
 * 参数校验.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ParamBuilder
{
    /**
     * @param string $name
     * @param mixed|null $default
     * @param string|null $type
     * @param string|null $parser
     * @return ParamBuilder
     */
    public function define(
        string $name,
        $default = null,
        $type = null,
        $parser = null
    ) : ParamBuilder;

    /**
     * @return ParamDefCollection
     */
    public function getParams() : ParamDefCollection;

}