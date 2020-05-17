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
 * 输入消息的向量表示.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Vector
{
    /**
     * @param float[] $vector
     */
    public function setVector(array $vector) : void;

    /**
     * @return float[]
     */
    public function getVector() : ? array;

    /**
     * @return bool
     */
    public function hasVector() : bool;

    /**
     * @param array $vector
     * @return float
     */
    public function cosineSimilarity(array $vector) : float;
}