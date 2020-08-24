<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\SpaCyNLU\Protocals;

use Commune\Support\Struct\AStruct;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $matched
 * @property-read string $reply
 * @property-read float $likely
 */
class ChatReplyData extends AStruct
{
    public static function stub(): array
    {
        return [
            'matched' => '',
            'reply' => '',
            'likely' => 0.0
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public function isEmpty() : bool
    {
        $reply = $this->reply;
        return empty($reply);
    }


}