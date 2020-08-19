<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\NLU;

/**
 * 文本分类工具.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface SentenceClassifier extends NLUService
{

    public function learn(string $text, string $type);

    public function predict(string $text) : ? string;

}