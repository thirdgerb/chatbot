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

use Commune\Blueprint\Ghost\Mind\Definitions\MemoryDef;
use Commune\Blueprint\Ghost\Mind\Registries\MemoryReg;
use Commune\Ghost\Mind\Metas\MemoryMeta;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IMemoryReg extends AbsDefRegistry implements MemoryReg
{
    protected function getDefType(): string
    {
        return MemoryDef::class;
    }

    public function getMetaId(): string
    {
        return MemoryMeta::class;
    }


}