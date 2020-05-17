<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\Abstracted;

/**
 * 将输入消息理解成为一种选择
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
interface Choice
{
    public function getChoice();

    public function hasChoice($choice) : bool;

    public function getAnswer() : string;

    public function addChoice($choice, string $answer) : void;
}