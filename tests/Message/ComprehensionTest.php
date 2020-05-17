<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\Message;

use Commune\Message\Abstracted\IComprehension;
use Commune\Protocals\Comprehension;
use Commune\Support\Babel\Babel;
use PHPUnit\Framework\TestCase;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ComprehensionTest extends TestCase
{

    public function testIComprehension()
    {
        $com = new IComprehension();
        $this->checkBabelSerialize($com);


        // vector
        $vector = [0.11, 0.22, 0.33];
        $this->assertFalse($com->vector->hasVector());
        $this->assertNull($com->vector->getVector());
        $this->assertEquals(0, $com->vector->cosineSimilarity($vector));
        $com->vector->setVector($vector);
        $this->assertTrue($com->vector->hasVector());
        $this->assertEquals($vector, $com->vector->getVector());
        $this->assertEquals(1, $com->vector->cosineSimilarity($vector));

        // 余弦算法.

        // emotions
        $emotions = ['positive', 'agreed'];
        $com->emotion->addEmotion(...$emotions);
        $this->assertEquals($emotions, $com->emotion->getEmotions());
        $this->assertTrue($com->emotion->hasEmotion('agreed'));

        // cmd
        $this->assertFalse($com->command->hasCmdStr());
        $com->command->setCmdStr($command = 'hello world');
        $this->assertEquals($command, $com->command->getCmdStr());
        $this->assertEquals('hello', $com->command->getCmdName());

        // token
        $this->assertFalse($com->tokens->hasTokens());
        $com->tokens->addTokens($tokens = ['a', 'b', 'c']);
        $this->assertTrue($com->tokens->hasTokens());
        $this->assertEquals($tokens, $com->tokens->getTokens());
        $this->checkBabelSerialize($com);
    }



    protected function checkBabelSerialize(Comprehension $comprehension)
    {
        $str = Babel::serialize($comprehension);
        $un = Babel::unserialize($str);
        $str2 = Babel::serialize($un);
        $this->assertEquals($str, $str2);
    }
}