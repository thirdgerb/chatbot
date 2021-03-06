<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Runtime;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ProcessSerializer
{
    public function unserialize(string $string) : array;

    public function serialize(array $data) : string;
}