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

use Commune\Blueprint\Ghost\MindDef\SynonymDef;
use Commune\Blueprint\Ghost\MindReg\SynonymReg;
use Commune\Blueprint\Ghost\MindMeta\SynonymMeta;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ISynonymReg extends AbsDefRegistry implements SynonymReg
{
    protected function normalizeDefName(string $name): string
    {
        return $name;
    }

    protected function getDefType(): string
    {
        return SynonymDef::class;
    }

    public function getMetaId(): string
    {
        return SynonymMeta::class;
    }


}