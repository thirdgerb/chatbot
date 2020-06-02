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
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Ghost\MindMeta\StageMeta;
use Commune\Blueprint\Ghost\MindReg\StageReg;
use Commune\Ghost\Support\ContextUtils;

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
        if (parent::hasRegisteredMeta($defName)) {
            return true;
        }

        list($maybeContextName, $stage) = ContextUtils::divideContextNameFromStageName($defName);

        $contextReg = $this->mindset->contextReg();

        if ($contextReg->hasDef($defName)) {
            $contextDef = $contextReg->getDef($defName);
            $this->registerDef($contextDef->asStageDef());
            return true;

        }

        if (empty($maybeContextName)) {
            return false;
        }

        if (!$contextReg->hasDef($maybeContextName)) {
            return false;
        }

        $contextDef = $contextReg->getDef($maybeContextName);
        $stageDef = $contextDef->getPredefinedStage($stage);

        if (isset($stageDef)) {
            $this->registerDef($stageDef);
            return true;
        }

        return false;
    }

}