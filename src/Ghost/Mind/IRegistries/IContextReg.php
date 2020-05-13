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

use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Ghost\Mind\Defs\ContextDef;
use Commune\Blueprint\Ghost\Mind\Defs\Def;
use Commune\Ghost\Mind\Metas\ContextMeta;
use Commune\Blueprint\Ghost\Mind\Regs\ContextReg;


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
        return $success
            // 同时注册到 StageDef 中.
            && $this->mindset->stageReg()->registerDef($def->asStageDef(), $notExists);
    }

    protected function getDefType(): string
    {
        return ContextDef::class;
    }


}