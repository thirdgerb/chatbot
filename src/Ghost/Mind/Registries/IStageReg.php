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

use Commune\Blueprint\Ghost\Mind\Definitions\Def;
use Commune\Blueprint\Ghost\Mind\Definitions\StageDef;
use Commune\Blueprint\Ghost\Mind\Metas\StageMeta;
use Commune\Blueprint\Ghost\Mind\Registries\StageReg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IStageReg extends AbsDefRegistry implements StageReg
{
    protected function getDefType(): string
    {
        return StageDef::class;
    }

    public function getMetaId(): string
    {
        return StageMeta::class;
    }

    protected function hasRegisteredMeta(string $defName): bool
    {
        $hasRegistered = parent::hasRegisteredMeta($defName);
        if ($hasRegistered) {
            return true;
        }

        $contextReg = $this->mindset->contextReg();
        $hasContext = $contextReg->hasDef($defName);
        if ($hasContext) {
            $this->registerDef($contextReg->getDef($defName)->asStageDef(), false);
            return true;
        }

        return false;
    }

    protected function getRegisteredMetaIds(): array
    {
        $contextIds = $this->mindset->contextReg()->getAllDefIds();
        $stageIds = parent::getRegisteredMetaIds();
        return array_merge($stageIds, $contextIds);
    }

    /**
     * @param StageDef $def
     * @param bool $notExists
     * @return bool
     */
    protected function doRegisterDef(Def $def, bool $notExists): bool
    {
        $success = parent::doRegisterDef($def, $notExists);

        $intentDef = $def->asIntentDef();
        $this->mindset->intentReg()->registerDef($intentDef, $notExists);
        return $success;
    }


}