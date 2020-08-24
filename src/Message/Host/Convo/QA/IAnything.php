<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Host\Convo\QA;

use Commune\Protocals\HostMsg\Convo\QA\AnswerMsg;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IAnything extends IQuestionMsg
{
    const MODE = self::MATCH_INDEX
        | self::MATCH_SUGGESTION
        | self::MATCH_INTENT
        | self::MATCH_ANY;

    protected function acceptAnyVerbalAnswer(VerbalMsg $message): ? AnswerMsg
    {
        $choice = $this->default;
        $answer = $this->suggestions[$choice] ?? '';
        return $this->newAnswer(
            $answer,
            $choice
        );
    }
}