<?php


namespace Commune\Test\Chatbot\App\Messages;


use Commune\Chatbot\App\Messages\QA\Choice;
use Commune\Chatbot\App\Messages\QA\Choose;
use Commune\Chatbot\App\Messages\Text;
use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;
use Commune\Chatbot\OOHost\Session\Session;
use PHPUnit\Framework\TestCase;

class ChooseTest extends TestCase
{

    /**
     */
    public function testChoice()
    {
        $c = new Choose('test', [
            'a',
            'b',
        ]);

        $session = \Mockery::mock(Session::class);
        $session->expects('getPossibleIntent')->andReturn(null);

        /**
         * @var \stdClass $incoming
         */
        $incoming = \Mockery::mock(IncomingMessage::class);


        /**
         * @var \stdClass $session
         */
        $session->incomingMessage = $incoming;
        $incoming->message = new Text('1');


        $a = $c->parseAnswer($session);
        $this->assertTrue($a instanceof Choice);
        $this->assertEquals('b', $a->toResult());
    }

    public function testPartMatch()
    {

        $c = new Choose('test', $choices = [
            'abcdefghi',
            'fghijklmn',
        ]);

        $session = \Mockery::mock(Session::class);
        $session->expects('getPossibleIntent')->andReturn(null);

        /**
         * @var \stdClass $incoming
         */
        $incoming = \Mockery::mock(IncomingMessage::class);


        /**
         * @var \stdClass $session
         */
        $session->incomingMessage = $incoming;

        // case 1
        $incoming->message = new Text('bcde'); // 没有'a', 不必从a开始.
        $a = $c->parseAnswer($session);
        $this->assertTrue($a instanceof Choice);
        $this->assertEquals(0, $a->getChoice());
        $this->assertEquals($choices[0], $a->toResult());

        // case 2
        $incoming->message = new Text('jklmn');
        $a = $c->parseAnswer($session);
        $this->assertTrue($a instanceof Choice);
        $this->assertEquals(1, $a->getChoice());
        $this->assertEquals($choices[1], $a->toResult());

        // case 3 same part
        $incoming->message = new Text('ghi');
        $a = $c->parseAnswer($session);
        $this->assertNull($a);

    }

    public function testPartIndexMatch()
    {
        $c = new Choose('test', $choices = [
            'abcdefghi' => 1,
            'fghijklmn' => 2,
        ]);

        $keys = array_keys($choices);

        $session = \Mockery::mock(Session::class);
        $session->expects('getPossibleIntent')->andReturn(null);

        /**
         * @var \stdClass $incoming
         */
        $incoming = \Mockery::mock(IncomingMessage::class);


        /**
         * @var \stdClass $session
         */
        $session->incomingMessage = $incoming;

        // case 1
        $incoming->message = new Text('bcde'); // 没有'a', 不必从a开始.
        $a = $c->parseAnswer($session);
        $this->assertTrue($a instanceof Choice);
        $this->assertEquals($keys[0], $a->getChoice());
        $this->assertEquals($choices[$keys[0]], $a->toResult());

        // case 2
        $incoming->message = new Text('jklmn');
        $a = $c->parseAnswer($session);
        $this->assertTrue($a instanceof Choice);
        $this->assertEquals($keys[1], $a->getChoice());
        $this->assertEquals($choices[$keys[1]], $a->toResult());

        // case 3 same part
        $incoming->message = new Text('ghi');
        $a = $c->parseAnswer($session);
        $this->assertNull($a);

    }

}

