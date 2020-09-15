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

use Commune\Blueprint\Ghost\MindDef\Def;
use Commune\Blueprint\Ghost\MindDef\IntentDef;
use Commune\Blueprint\Ghost\MindMeta\IntentMeta;
use Commune\Blueprint\Ghost\MindReg\IntentReg;
use Commune\Ghost\Support\ContextUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IIntentReg extends AbsDefRegistry implements IntentReg
{
    protected function normalizeDefName(string $name): string
    {
        return ContextUtils::normalizeIntentName($name);
    }

    protected function getDefType(): string
    {
        return IntentDef::class;
    }

    public function getMetaId(): string
    {
        return IntentMeta::class;
    }

    /**
     * @param IntentDef $def
     * @param bool $notExists
     * @return bool
     */
    public function registerDef(Def $def, bool $notExists = true): bool
    {
        if ($def->isEmpty()) {
            return true;
        }
        return parent::registerDef($def, $notExists);
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
        $intentDef = $stageDef->asIntentDef();
        if ($intentDef->isEmpty()) {
            return false;
        }
        $this->registerDef($intentDef);
        return true;
    }

    /**
     * @param IntentDef $def
     * @return bool
     */
    protected function alreadyHasDef(Def $def): bool
    {
        $name = $def->getName();
        /**
         * @var IntentDef $registered
         */
        $registered = $this->getDef($name);
        $changed = $registered->mergeDef($def);
        if ($changed) {
            $this->getMetaRegistry()->save($registered->toMeta());
        }
        return true;
    }
}