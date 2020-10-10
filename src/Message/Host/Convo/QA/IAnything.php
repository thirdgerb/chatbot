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

use Commune\Blueprint\Ghost\Cloner;
use Commune\Protocols\HostMsg\Convo\QA\AnswerMsg;
use Commune\Protocols\HostMsg\Convo\VerbalMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IAnything extends IQuestionMsg
{
    const MODE = self::MATCH_INDEX
        | self::MATCH_SUGGESTION
        | self::MATCH_INTENT
        | self::MATCH_ANY;

    protected $_matchedAny = false;

    protected function acceptAnyVerbalAnswer(VerbalMsg $message): ? AnswerMsg
    {
        $this->_matchedAny = true;
        return null;

    }

    public function match(Cloner $cloner): ? AnswerMsg
    {
        if ($this->_matchedAny) {
            $choice = $this->default;
            $answerText = $this->suggestions[$choice] ?? '';
            $answer = $this->newAnswer(
                $answerText,
                $choice
            );
            $this->setAnswerToComprehension($answer, $cloner->comprehension);
            return $answer;
        }

        return parent::match($cloner);
    }


}