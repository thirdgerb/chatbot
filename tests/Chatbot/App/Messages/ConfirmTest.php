<?php


namespace Commune\Test\Chatbot\App\Messages;


use Commune\Chatbot\App\Messages\QA\Confirm;
use Commune\Chatbot\App\Messages\QA\Confirmation;
use Commune\Chatbot\App\Mock\MockSession;
use PHPUnit\Framework\TestCase;

class ConfirmTest extends TestCase
{
    use MockSession;

    public function testConfirmWithDefault()
    {
        $confirm = new Confirm('要[开始](y)吗?', false, '是', '否');

        $session = $this->createSessionMocker('开始');

        $a = $confirm->parseAnswer($session);

        $this->assertTrue($a instanceof Confirmation);
        $this->assertTrue($a->toResult());
        $this->assertEquals(1, $a->getChoice());
        $this->assertTrue($confirm->getAnswer()->toResult());


        $session = $this->createSessionMocker('never');

        $a = $confirm->parseAnswer($session);
        $this->assertNull($a);
        $this->assertNull($confirm->getAnswer());

    }

}