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
 * 输入消息的情绪
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Emotion
{

    public function setEmotion(string $emotion, bool $bool) : void;

    public function addEmotions(string $emotion, string ...$emotions) : void;

    /**
     * @param string $emotionName
     * @return null|bool
     */
    public function hasEmotion(string $emotionName) : ? bool;

    /**
     * @return string[]
     */
    public function getEmotions() : array;
}