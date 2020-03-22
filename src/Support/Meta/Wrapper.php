<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Meta;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Wrapper
{
    const WRAP_METHOD = 'wrap';

    /**
     * @param Meta $meta
     * @return static
     */
    public static function wrap(Meta $meta) : Wrapper;

    public function getMeta() : Meta;

}