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

use Commune\Blueprint\Ghost\Mind\Defs\SynonymDef;
use Commune\Blueprint\Ghost\Mind\Regs\SynonymReg;
use Commune\Ghost\Mind\Metas\SynonymMeta;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ISynonymReg extends AbsDefRegistry implements SynonymReg
{
    protected function getDefType(): string
    {
        return SynonymDef::class;
    }

    public function getMetaId(): string
    {
        return SynonymMeta::class;
    }


}