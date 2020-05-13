<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Mind;

use Commune\Blueprint\Ghost\Mind\Registries\ContextReg;
use Commune\Blueprint\Ghost\Mind\Registries\EntityReg;
use Commune\Blueprint\Ghost\Mind\Registries\IntentReg;
use Commune\Blueprint\Ghost\Mind\Registries\MemoryReg;
use Commune\Blueprint\Ghost\Mind\Registries\StageReg;
use Commune\Blueprint\Ghost\Mind\Registries\SynonymReg;

/**
 * 对话机器人的基础思维
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Mindset
{

    /**
     * 清空所有的逻辑记忆.
     */
    public function reload() : void;

    public function contextReg() : ContextReg;

    public function intentReg() : IntentReg;

    public function stageReg() : StageReg;

    public function memoryReg() : MemoryReg;

    public function entityReg() : EntityReg;

    public function synonymReg() : SynonymReg;
}