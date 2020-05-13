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

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Mind\Defs\Def;
use Commune\Blueprint\Ghost\Mind\Defs\StageDef;
use Commune\Ghost\Mind\Metas\StageMeta;
use Commune\Blueprint\Ghost\Mind\Regs\StageReg;
use Commune\Support\Utils\StringUtils;


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
        if (isset($this->cachedDefs[$defName])) {
            return true;
        }

        list($prefix, $suffix) = StringUtils::dividePrefixAndName($defName, Context::NAMESPACE_SEPARATOR);

        // 先检查 Context 是否存在. 同时也会尝试重新注册 ContextDef
        $contextReg = $this->mindset->contextReg();
        // ContextName 可能是前缀, 可能是全名.
        $hasContext = $contextReg->hasDef($defName) || $contextReg->hasDef($prefix);
        if (!$hasContext) {
            return false;
        }

        return parent::hasRegisteredMeta($defName);
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