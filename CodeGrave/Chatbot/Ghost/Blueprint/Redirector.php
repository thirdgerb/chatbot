<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Ghost\Blueprint;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Redirector
{

    public function ticking() : int;

    public function dialog() : Dialog;

    /**
     * @param Ghost $ghost
     * @return Redirector|null
     */
    public function invoke(Ghost $ghost) : ? Redirector;

}