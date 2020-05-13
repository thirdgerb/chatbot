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
use Commune\Blueprint\Ghost\Mind\Registries\DefRegistry;
use Commune\Blueprint\Ghost\Mind\Registries\EmotionReg;
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
     * 初始化所有的 Contexts
     */
    public function initContexts() : void;

    /**
     * 清空所有的逻辑记忆.
     */
    public function reload() : void;

    /**
     * 上下文语境注册表.
     * @return ContextReg
     */
    public function contextReg() : DefRegistry;

    /**
     * Stage 注册表
     * @return StageReg
     */
    public function stageReg() : DefRegistry;

    /**
     * 意图注册表.
     * @return IntentReg
     */
    public function intentReg() : DefRegistry;

    /**
     * @return MemoryReg
     */
    public function memoryReg() : DefRegistry;

    /**
     * @return EmotionReg
     */
    public function emotionReg() : DefRegistry;

    /**
     * @return EntityReg
     */
    public function entityReg() : DefRegistry;

    /**
     * @return SynonymReg
     */
    public function synonymReg() : DefRegistry;

}