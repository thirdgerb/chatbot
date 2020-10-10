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

use Commune\Message\Host\Convo\IText;
use Commune\Message\Host\Convo\QA\IConfirm;
use Commune\Message\Host\Convo\QA\IConfirmation;
use Commune\Message\Host\Convo\QA\IQuestionMsg;
use Commune\Message\Intercom\IInputMsg;
use Commune\Protocols\HostMsg\Convo\QA\Confirmation;
use Commune\Protocols\HostMsg\Convo\QA\QuestionMsg;
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

    public function testConfirm()
    {
        $q = new IConfirm();

        $this->assertEquals(
            IConfirm::MATCH_SUGGESTION
            | IConfirm::MATCH_INTENT
            | IConfirm::MATCH_INDEX,
            $q->mode
        );

        $this->assertEquals(0, QuestionMsg::MATCH_ANY & $q->mode);

        $this->assertEquals(QuestionMsg::MATCH_SUGGESTION, $q->mode & QuestionMsg::MATCH_SUGGESTION);


        $q = IConfirm::newConfirm('test', null, 'y', 'n');

        $input = IInputMsg::instance(
            IText::instance('n'),
            'test'
        );

        $a = $q->parseInput($input);
        $this->assertTrue($a instanceof Confirmation);
        $this->assertTrue($a->isNegative());
    }


}