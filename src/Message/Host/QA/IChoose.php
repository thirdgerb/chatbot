<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Host\QA;

use Commune\Protocals\HostMsg\Convo\QA\AnswerMsg;
use Commune\Protocals\HostMsg\Convo\QA\Choose;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IChoose extends IQuestionMsg implements Choose
{
    public function __construct(string $query, string $default = null, array $suggestions = [], array $routes = [])
    {
        parent::__construct($query, $default, $suggestions, $routes);
    }

    protected function acceptAnyAnswer(VerbalMsg $message) : ? AnswerMsg
    {
        return null;
    }

    protected function newAnswer(string $answer, string $choice = null): AnswerMsg
    {
        return new IChoice(['answer' => $answer, 'choice' => $choice]);
    }
}