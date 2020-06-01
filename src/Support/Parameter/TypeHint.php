<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Parameter;


/**
 * 自定义的类型约束.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface TypeHint
{

    public function validate($value, string ...$params) : bool;

    public function parse($value, string ...$params);

}