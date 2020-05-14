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

use Commune\Blueprint\Ghost\MindDef\MemoryDef;
use Commune\Blueprint\Ghost\MindReg\MemoryReg;
use Commune\Blueprint\Ghost\MindMeta\MemoryMeta;

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

    protected function hasRegisteredMeta(string $defName): bool
    {
        return parent::hasRegisteredMeta($defName)
            || $this->mindset->contextReg()->hasDef($defName);
    }


}