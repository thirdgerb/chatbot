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
use Commune\Blueprint\Ghost\MindMeta\Option\ParamOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ParamBuilder
{

    /**
     * @param string $name
     * @param string $query
     * @param mixed|null $default
     * @param string|null $type
     * @param string|null $parser
     * @return ParamBuilder
     */
    public function add(
        string $name,
        $default = null,
        $type = null,
        $parser = null,
        string $query = ''
    ) : ParamBuilder;

    public function dependOn(
        string $name,
        string $contextName
    ) : ParamBuilder;


    /**
     * @return ParamOption[]
     */
    public function toParamOptions() : array;
}