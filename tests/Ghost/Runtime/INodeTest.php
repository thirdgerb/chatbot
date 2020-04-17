<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\Ghost\Runtime;

use Commune\Ghost\Prototype\Runtime\INode;
use PHPUnit\Framework\TestCase;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class INodeTest extends TestCase
{
    public function testToArray()
    {
        $node = new INode('abc.efg.hij', 'id', 10);

        $this->assertEquals(
            [
                "contextName" => "abc.efg.hij",
                "contextId" => "id",
                "priority" => 10,
                "stageName" => "",
                "stack" => [],
            ],
            $node->toArray()
        );

        $this->assertEquals('abc.efg.hij', $node->getStageFullname());

        $node->pushStack(['t1', 't2', 't3']);
        $this->assertEquals('', $node->stageName);
        $this->assertTrue($node->next());
        $this->assertEquals('t1', $node->stageName);
        $this->assertTrue($node->next());
        $this->assertEquals('t2', $node->stageName);
        $this->assertTrue($node->next());
        $this->assertEquals('t3', $node->stageName);
        $this->assertFalse($node->next());
    }

}