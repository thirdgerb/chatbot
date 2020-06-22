<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\Shell;

use Commune\Host\Prototype\ShellProtoConfig;
use Commune\Support\Protocal\ProtocalOption;
use PHPUnit\Framework\TestCase;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ShellConfigTest extends TestCase
{

    public function testProtocals()
    {
        $c = new ShellProtoConfig();

        $protocals = $c->protocals;

        $this->assertTrue(count($protocals) > 0);

        $protocal = current($protocals);
        $this->assertTrue($protocal instanceof ProtocalOption);
    }

}