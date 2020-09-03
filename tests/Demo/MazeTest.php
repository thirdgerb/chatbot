<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Test\Demo;

use Commune\Components\Demo\Maze\Logic\Manager;
use PHPUnit\Framework\TestCase;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class MazeTest extends TestCase
{


    public function testToward()
    {
        foreach (Manager::DIRECTIONS as $d) {
            $n = Manager::parseTowardToDirection($d, Manager::TOWARD_FRONT);
            $this->assertEquals($d, $n);
        }

        foreach (Manager::DIRECTIONS as $d) {
            $n = Manager::parseTowardToDirection($d, Manager::TOWARD_LEFT);
            $this->assertEquals((4 + $d -1) %4 , $n);
        }
    }

}