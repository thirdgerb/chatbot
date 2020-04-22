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

    /**
     * 注册一个注册表实例.
     * 相同类型的注册表, 可以拥有多个实例, 从不同的来源获取相同的信息.
     *
     * @param DefRegistry $reg
     */
    public function registerReg(DefRegistry $reg) : void;

    public function commandReg() : CommandReg;

    public function contextReg() : ContextReg;

    public function intentReg() : IntentReg;

    public function stageReg() : StageReg;
}