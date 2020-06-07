<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IMindReg;

use Commune\Blueprint\Ghost\MindDef\EmotionDef;
use Commune\Blueprint\Ghost\MindReg\EmotionReg;
use Commune\Blueprint\Ghost\MindMeta\EmotionMeta;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IEmotionReg extends AbsDefRegistry implements EmotionReg
{
    protected function normalizeDefName(string $name): string
    {
        return $name;
    }

    protected function getDefType(): string
    {
        return EmotionDef::class;
    }

    public function getMetaId(): string
    {
        return EmotionMeta::class;
    }


}