<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Packers;

use Commune\Blueprint\Platform\Packer;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface TcpPacker extends Packer
{

    /**
     * @return string
     */
    public function input() : string;

    /**
     * @param string $output
     */
    public function output(string $output) : void;

}