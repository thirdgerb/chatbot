<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Mind\Registries;

use Commune\Blueprint\Ghost\Mind\Definitions\IntentDef;
use Commune\Blueprint\Ghost\Mind\Metas\IntentMeta;
use Commune\Blueprint\Ghost\Mind\Registries\IntentReg;


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
        $hasRegistered = parent::hasRegisteredMeta($defName);
        if ($hasRegistered) {
            return true;
        }

        $stageReg = $this->mindset->stageReg();
        $hasStage = $stageReg->hasDef($defName);
        if ($hasStage) {
            $this->registerDef($stageReg->getDef($defName)->asIntentDef(), false);
            return true;
        }

        return false;
    }


    protected function getRegisteredMetaIds(): array
    {
        $stageIds = $this->mindset->stageReg()->getAllDefIds();
        $intentIds = parent::getRegisteredMetaIds();
        return array_merge($stageIds, $intentIds);
    }

}