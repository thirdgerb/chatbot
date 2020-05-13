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

use Commune\Blueprint\Ghost\Mind\Definitions\EntityDef;
use Commune\Blueprint\Ghost\Mind\Registries\EntityReg;
use Commune\Ghost\Mind\Metas\EntityMeta;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IEntityReg extends AbsDefRegistry implements EntityReg
{
    protected function getDefType(): string
    {
        return EntityDef::class;
    }

    public function getMetaId(): string
    {
        return EntityMeta::class;
    }


}