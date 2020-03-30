<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Blueprint\Abstracted;

use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Support\Babel\BabelSerializable;

/**
 * 选择结果. 如果是 conversational 的消息, 结果应该是可选的.
 *
 * suggestions 包含几个方面: index suggestion answer
 * answer 是真正的抽象, 可以用于后续的逻辑.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Choice extends ArrayAndJsonAble
{
    public function countChoices() : int;

    public function addChoice($index, string $answer) : void;

    public function getAnswers() : array;

    public function getChoices() : array;

    public function hasIndex($index, bool $only = false) : bool;

    public function hasAnswer(string $answer, bool $only = false) : bool;

}