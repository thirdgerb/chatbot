<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\MindReg;

use Commune\Blueprint\Ghost\MindDef\EmotionDef;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @method EmotionDef getDef(string $defName) : Def
 */
interface EmotionReg extends DefRegistry
{
    const EMO_POSITIVE = 'emotion.positive';
    const EMO_NEGATIVE = 'emotion.negative';

}