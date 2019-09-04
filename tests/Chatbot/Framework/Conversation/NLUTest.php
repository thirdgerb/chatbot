<?php


namespace Commune\Test\Chatbot\Framework\Conversation;


use Commune\Chatbot\Framework\Conversation\NatureLanguageUnit;
use PHPUnit\Framework\TestCase;

class NLUTest extends TestCase
{

    public function testHighlyPossibleFunc()
    {
        $nlu = new NatureLanguageUnit();

        $nlu->addPossibleIntent('a',  1);
        $nlu->addPossibleIntent('b', 2);
        $nlu->addPossibleIntent('c', 3, false);
        $nlu->addPossibleIntent('d', 4);

        $this->assertEquals('d', $nlu->getMostPossibleIntent());

        $this->assertEquals(['d', 'c', 'b', 'a'], $nlu->getPossibleIntentNames(false));
        $this->assertEquals(['d', 'b',  'a'], $nlu->getPossibleIntentNames(true));


    }


}