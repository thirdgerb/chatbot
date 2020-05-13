<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Mind\IRegistries;

use Commune\Blueprint\Ghost\Mind\Definitions\EmotionDef;
use Commune\Blueprint\Ghost\Mind\Registries\EmotionReg;
use Commune\Ghost\Mind\Metas\EmotionMeta;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IEmotionReg extends AbsDefRegistry implements EmotionReg
{
    protected function getDefType(): string
    {
        return EmotionDef::class;
    }

    public function getMetaId(): string
    {
        return EmotionMeta::class;
    }


}