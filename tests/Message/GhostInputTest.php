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

use Commune\Message\Host\Convo\IText;
use Commune\Message\Intercom\IShellInput;
use Commune\Protocals\Intercom\ShellMsg;
use Commune\Support\Babel\Babel;
use PHPUnit\Framework\TestCase;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class GhostInputTest extends TestCase
{

    public function testShellToGhost()
    {
        $shellInput = new IShellInput(
            new IText('hello world'),
            'test',
            'testUser',
            'testId',
            null,
            [
                'senderName' => 'testName',
                'sceneId' => 'testScene',
                'sessionId' => null,
                'env' => ['a' => 1],
                'deliverAt' => 3.2,
                'createdAt' => 3.1,
            ]
        );

        $shellInputStr = Babel::serialize($shellInput);
        /**
         * @var ShellMsg $shellInput2
         */
        $shellInput2 = Babel::unserialize($shellInputStr);

        $this->assertEquals($shellInput->toArray(), $shellInput2->toArray());
//
//        $ghostInput = $shellInput->toGhostInput(
//            'testCloneId',
//            'testSessionId',
//            'testGuestId'
//        );


    }

}