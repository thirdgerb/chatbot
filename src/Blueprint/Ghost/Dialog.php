<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read Cloner $cloner
 * @property-read Context $context
 * @property-read Ucl $ucl
 * @property-read Dialog|null $prev
 */
interface Dialog
{

    /**
     * 尝试退出当前多轮对话, 可能被拦截.
     * @return Dialog
     */
    public function quit() : Dialog;

    /**
     * Dialog 逻辑运行一帧.
     * @return Dialog
     */
    public function tick() : Dialog;

}