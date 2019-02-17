<?php

/**
 * Class MessageCommandTest
 * @package Commune\Chatbot\Test\Framework\Conversation
 */

namespace Commune\Chatbot\Test\Framework\Conversation;

use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class MessageCommandTest extends TestCase
{


    public function testCollection()
    {
        $c = new Collection([
            'a' => []
        ]);

        $t = $c['a'];
        $t['k'] = 1;
        $c['a'] = $t;

        $this->assertEquals(1, $c['a']['k']);
    }


}