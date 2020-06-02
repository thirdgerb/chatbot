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

use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\MindMeta\ContextMeta;
use Commune\Blueprint\Ghost\MindReg\ContextReg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IContextReg extends AbsDefRegistry implements ContextReg
{
    public function getMetaId(): string
    {
        return ContextMeta::class;
    }

    protected function getDefType(): string
    {
        return ContextDef::class;
    }


}