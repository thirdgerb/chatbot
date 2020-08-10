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
use Commune\Blueprint\Ghost\MindDef\Def;
use Commune\Blueprint\Ghost\MindMeta\ContextMeta;
use Commune\Blueprint\Ghost\MindReg\ContextReg;
use Commune\Ghost\Support\ContextUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IContextReg extends AbsDefRegistry implements ContextReg
{
    protected function normalizeDefName(string $name): string
    {
        return ContextUtils::normalizeContextName($name);
    }

    public function getMetaId(): string
    {
        return ContextMeta::class;
    }

    protected function getDefType(): string
    {
        return ContextDef::class;
    }

    /**
     * @param ContextDef $def
     * @param bool $notExists
     * @return bool
     */
    public function registerDef(Def $def, bool $notExists = true): bool
    {
        $success = parent::registerDef($def, $notExists);
        $force = !$notExists;

        if ($force && $success) {

            // 强制存储时, 必须刷新掉可能存在的 stage 配置
            $stageReg = $this->mindset->stageReg();
            foreach ($def->eachPredefinedStage() as $stageDef) {
                $stageReg->registerDef($stageDef, false);
            }
            $stageReg->registerDef($def->asStageDef(), false);

            // 强制存储时, 刷新 memory 配置.
            $memoryDef = $def->asMemoryDef();
            $memoryReg = $this->mindset->memoryReg();
            $memoryReg->registerDef($memoryDef, false);
        }
        return $success;
    }

}