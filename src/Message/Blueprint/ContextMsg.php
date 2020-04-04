<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Blueprint;


/**
 * 用于同步当前的 Context.
 * 通常不一定要渲染.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ContextMsg extends Message
{
    /**
     * @return string
     */
    public function getContextId() : string;

    /**
     * @return string
     */
    public function getContextName() : string;

    /**
     * @return array
     */
    public function getEntities() : array;

}