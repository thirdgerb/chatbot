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

use Commune\Blueprint\Ghost\MindDef\ChatDef;
use Commune\Blueprint\Ghost\MindMeta\ChatMeta;
use Commune\Blueprint\Ghost\MindReg\ChatReg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IChatReg extends AbsDefRegistry implements ChatReg
{
    protected function getDefType(): string
    {
        return ChatDef::class;
    }

    protected function normalizeDefName(string $name): string
    {
        return $name;
    }

    public function getMetaId(): string
    {
        return ChatMeta::class;
    }


}