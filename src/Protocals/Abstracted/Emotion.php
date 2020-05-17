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

use Commune\Support\Protocal\Protocal;


/**
 * 输入消息的情绪
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Emotion extends Protocal
{
    /**
     * @param string ...$emotion
     */
    public function addEmotion(string ...$emotion) : void;

    /**
     * @param string $emotionName
     * @return bool
     */
    public function hasEmotion(string $emotionName) : bool;

    /**
     * @return string[]
     */
    public function getEmotions() : array;
}