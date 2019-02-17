<?php

/**
 * Class QuestionCfgTest
 * @package Commune\Chatbot\Test\Framework\Context
 */

namespace Commune\Chatbot\Test\Framework\Context;


use Commune\Chatbot\Framework\Context\Predefined\Answer;
use Commune\Chatbot\Framework\Context\Predefined\Choice;
use Commune\Chatbot\Framework\Context\Predefined\Confirmation;
use Commune\Chatbot\Framework\Context\Predefined\Question;
use PHPUnit\Framework\TestCase;

class QuestionCfgTest extends TestCase
{
    public function testExtends()
    {
        $this->assertEquals(Question::SCOPE, Answer::SCOPE);
        $this->assertEquals(Question::SCOPE, Confirmation::SCOPE);
        $this->assertEquals(Question::SCOPE, Choice::SCOPE);
    }

}