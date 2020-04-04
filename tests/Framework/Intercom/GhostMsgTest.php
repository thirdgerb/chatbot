<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\Framework\Intercom;

use Commune\Framework\Prototype\Intercom\IGhostInput;
use Commune\Framework\Prototype\Intercom\IShellInput;
use Commune\Framework\Prototype\Intercom\IShellScope;
use Commune\Message\Prototype\IText;
use Commune\Support\Babel\Babel;
use PHPUnit\Framework\TestCase;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class GhostMsgTest extends TestCase
{

    public function testInput()
    {
        $input = new IGhostInput(
            'test',
            'cid',
            new IShellInput(
                new IText('text'),
                new IShellScope([])
            ),
            'tid',
            'sid',
            $hello = [
                'hello' => 'world'
            ]
        );

        // 传输第一次
        $s = Babel::getResolver()->serialize($input);
        $input = Babel::getResolver()->unSerialize($s);

        // 传输第二次
        $s = Babel::getResolver()->serialize($input);
        $input = Babel::getResolver()->unSerialize($s);

        $this->assertEquals('test', $input->shellName);
        $this->assertEquals('cid', $input->chatId);
        $this->assertEquals('text', $input->getTrimmedText());
        $this->assertEquals('tid', $input->traceId);
        $this->assertEquals('cid', $input->sceneId);
        $this->assertEquals($hello, $input->sceneEnv);

    }

}