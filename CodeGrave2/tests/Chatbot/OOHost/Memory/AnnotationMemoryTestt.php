<?php


namespace Commune\Test\Chatbot\OOHost\Memory;


use Commune\Chatbot\OOHost\Context\Memory\AnnotationMemory;
use Commune\Chatbot\OOHost\Context\Memory\MemoryData;
use Commune\Chatbot\OOHost\Session\Scope;
use PHPUnit\Framework\TestCase;

class AnnotationMemoryTest extends TestCase
{

    public function testParameter()
    {
        $scope = new Scope([Scope::CHAT_ID => 'chat']);
        $memory = new TestM($scope, $data = new MemoryData());

        $this->assertEquals(
            ['a', 'b', 'c'],
            $memory->getDefinition()->getParameterKeys()
        );

        $d = $memory->getDefinition();

        $this->assertEquals(
            '',
            $d->getParameter('b')->getDescription()
        );

        $this->assertEquals(
            'test desc',
            $d->getParameter('a')->getDescription()
        );

        $this->assertEquals(
            'a',
            $memory->depending()->getName()
        );

        $this->assertFalse($memory->isPrepared());

        $memory->a = 123;
        $memory->b = 'abc';
        $memory->c = true;

        $this->assertTrue($memory->isPrepared());
        $this->assertEquals(123, $memory->a);
        $this->assertEquals('abc', $memory->b);
        $this->assertTrue($memory->c);


        $this->assertEquals(
            $memory->getScopingId(),
            $scope->makeScopingId(TestM::class, [Scope::CHAT_ID])
        );
    }

}


/**
 * @property int $a test desc
 * @property string $b
 * @property bool $c
 */
class TestM extends AnnotationMemory
{
    const SCOPE_TYPES = [Scope::CHAT_ID];

}