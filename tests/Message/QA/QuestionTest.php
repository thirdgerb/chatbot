<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\Message\QA;

use Commune\Message\Host\Convo\QA\IQuestionMsg;
use Commune\Protocals\HostMsg\Convo\QA\QuestionMsg;
use PHPUnit\Framework\TestCase;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class QuestionTest extends TestCase
{

    public function testMatchMode()
    {
        $question = IQuestionMsg::instance('query', null, ['a', 'b', 'c'], []);

        $this->assertEquals(
            QuestionMsg::MATCH_INDEX
            | QuestionMsg::MATCH_SUGGESTION
            | QuestionMsg::MATCH_INTENT
            | QuestionMsg::MATCH_ANY,
            $question->getMatchMode()
        );

        $this->assertTrue($question->isMatchMode(QuestionMsg::MATCH_SUGGESTION));
        $this->assertTrue($question->isMatchMode(QuestionMsg::MATCH_ANY));
        $this->assertTrue($question->isMatchMode(QuestionMsg::MATCH_INDEX));
        $this->assertTrue($question->isMatchMode(QuestionMsg::MATCH_INTENT));

        $question->withoutMatchMode(QuestionMsg::MATCH_SUGGESTION);
        $this->assertFalse($question->isMatchMode(QuestionMsg::MATCH_SUGGESTION));
        $this->assertTrue($question->isMatchMode(QuestionMsg::MATCH_INDEX));
    }

}