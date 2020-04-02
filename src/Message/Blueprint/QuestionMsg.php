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

use Commune\Message\Blueprint\Tag\Conversational;
use Commune\Message\Blueprint\Tag\Verbal;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface QuestionMsg extends Message, Conversational, Verbal
{
    /**
     * @return bool
     */
    public function isNullable() : bool;

    public function allowAny() : bool;
}