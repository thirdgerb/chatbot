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
use Commune\Support\Protocol\ProtocolOption;
use PHPUnit\Framework\TestCase;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ShellConfigTest extends TestCase
{

    public function testProtocols()
    {
        $c = new ShellProtoConfig();

        $Protocols = $c->Protocols;

        $this->assertTrue(count($Protocols) > 0);

        $Protocol = current($Protocols);
        $this->assertTrue($Protocol instanceof ProtocolOption);
    }

}