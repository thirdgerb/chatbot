<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Mind;


/**
 * 对话机器人的思维.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Mindset
{

    /**
     * 清空所有的逻辑记忆.
     */
    public function reload() : void;


    public function commandReg() : CommandReg;

    public function contextReg() : ContextReg;

    public function stageReg() : StageReg;
}