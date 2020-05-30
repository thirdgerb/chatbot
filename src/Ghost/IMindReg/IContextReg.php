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


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IContextReg extends AbsDefRegistry implements ContextReg
{
    public function getMetaId(): string
    {
        return ContextMeta::class;
    }

    /**
     * @param ContextDef $def
     * @param bool $notExists
     * @return bool
     */
    protected function doRegisterDef(Def $def, bool $notExists) : bool
    {
        $success = parent::doRegisterDef($def, $notExists);

        $stageReg = $this->mindset->stageReg();
        $success = $success
            // 同时注册到 StageDef 中.
            && $stageReg->registerDef($def->asStageDef(), $notExists)
            && $this->mindset->memoryReg()->registerDef($def->asMemoryDef(), $notExists);

        if ($success) {
            $names = $def->getPredefinedStageNames();
            foreach ($names as $name) {
                $stageReg->registerDef($def->getPredefinedStage($name), true);
                return $name;
            }
        }

        return $success;
    }

    protected function getDefType(): string
    {
        return ContextDef::class;
    }


}