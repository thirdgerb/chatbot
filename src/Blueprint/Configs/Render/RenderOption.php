<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Configs\Render;

use Commune\Support\Option;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RenderOption extends Option
{
    const IDENTITY = '';

    public static function stub(): array
    {
        return [
            'template' => 'template class name',
            'listen' => [
                'exactly.reactionId',
                'wildcard.reactionId.*',
            ],
        ];
    }

}