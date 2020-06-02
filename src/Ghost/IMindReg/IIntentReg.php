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

use Commune\Blueprint\Ghost\MindDef\IntentDef;
use Commune\Blueprint\Ghost\MindMeta\IntentMeta;
use Commune\Blueprint\Ghost\MindReg\IntentReg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IIntentReg extends AbsDefRegistry implements IntentReg
{
    protected function getDefType(): string
    {
        return IntentDef::class;
    }

    public function getMetaId(): string
    {
        return IntentMeta::class;
    }

    protected function hasRegisteredMeta(string $defName): bool
    {
        // 先检查 Stage, 会尝试注册 Context
        $stageReg = $this->mindset->stageReg();

        if (parent::hasRegisteredMeta($defName)) {
            return true;
        }

        if (!$stageReg->hasDef($defName)) {
            return false;
        }

        $stageDef = $stageReg->getDef($defName);
        $this->registerDef($stageDef->asIntentDef());
        return true;
    }
}