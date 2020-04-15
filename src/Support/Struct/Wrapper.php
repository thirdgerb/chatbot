<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Struct;


/**
 * 通过 Meta 实例化出来的对象.
 * 至于实例化的方式, 不需要在这里设定.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Wrapper
{
    public function getMeta() : Meta;
}