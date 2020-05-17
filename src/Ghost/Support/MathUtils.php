<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Support;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class MathUtils
{

    public static function cosineSimilarity(array $vector, array $base) : float
    {
        // todo , 回头写一个实现.
        if ($vector === $base) {
            return 1;
        }
        return 0;
    }

}