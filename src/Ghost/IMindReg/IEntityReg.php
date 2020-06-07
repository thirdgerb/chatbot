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

use Commune\Blueprint\Ghost\MindDef\EntityDef;
use Commune\Blueprint\Ghost\MindReg\EntityReg;
use Commune\Blueprint\Ghost\MindMeta\EntityMeta;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IEntityReg extends AbsDefRegistry implements EntityReg
{
    protected function normalizeDefName(string $name): string
    {
        return $name;
    }

    protected function getDefType(): string
    {
        return EntityDef::class;
    }

    public function getMetaId(): string
    {
        return EntityMeta::class;
    }


}