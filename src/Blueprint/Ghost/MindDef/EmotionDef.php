<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\MindDef;

use Commune\Blueprint\Ghost\Cloner;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface EmotionDef extends Def
{
    const EMO_POSITIVE = 'emotion.positive';
    const EMO_NEGATIVE = 'emotion.negative';

    /**
     * @param Cloner $cloner
     * @param array $injectionContext
     * @return bool
     */
    public function feels(
        Cloner $cloner,
        array $injectionContext = []
    ): bool;
}