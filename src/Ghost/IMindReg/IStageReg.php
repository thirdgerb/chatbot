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
use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IStageReg extends AbsDefRegistry implements StageReg
{
    protected function normalizeDefName(string $name): string
    {
        return ContextUtils::normalizeStageName($name);
    }

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
        // 已经注册过了.
        if (parent::hasRegisteredMeta($defName)) {
            return true;
        }

        list($contextName, $stageName) = ContextUtils::separateContextAndStageFromFullname($defName);

        // 如果当前 def 名就是 context 的 name
        $contextReg = $this->mindset->contextReg();
        if (
            StringUtils::isEmptyStr($stageName)
            && $contextReg->hasDef($contextName)
        ) {
            $contextDef = $contextReg->getDef($defName);
            $this->registerDef($contextDef->asStageDef());
            return true;
        }

        if (empty($contextName)) {
            return false;
        }

        if (!$contextReg->hasDef($contextName)) {
            return false;
        }

        $contextDef = $contextReg->getDef($contextName);
        $stageDef = $contextDef->getPredefinedStage($stageName);

        if (isset($stageDef)) {
            $this->registerDef($stageDef);
            return true;
        }

        return false;
    }

    /**
     * @param StageDef $def
     * @param bool $notExists
     * @return bool
     */
    public function registerDef(Def $def, bool $notExists = true): bool
    {
        $success =  parent::registerDef($def, $notExists);

        // 强制注册时, 仍然不能刷新掉关联的 intent
        // 否则已经定义过的 examples 等就都丢失了
        if ($success) {
            $intentDef = $def->asIntentDef();
            $this->mindset->intentReg()->registerDef(
                $intentDef,
                true
            );
        }
        return $success;
    }
}