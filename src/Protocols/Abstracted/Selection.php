<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocols\Abstracted;

/**
 * 将输入消息理解成为多种选择.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
interface Selection
{
    /**
     * @param string[] $selections
     * @return void
     */
    public function setSelections(array $selections) : void;

    /**
     * @return string[]
     */
    public function getSelections() : array;

    /**
     * @param string $choice
     * @return bool
     */
    public function isSelected(string $choice) : bool;

}