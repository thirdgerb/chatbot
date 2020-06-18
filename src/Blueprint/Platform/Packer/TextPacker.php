<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Platform\Packer;

use Commune\Blueprint\Platform\Packer;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface TextPacker extends Packer
{

    /**
     * @return string
     */
    public function getRequest() : string;

    /**
     * @param string[] $response
     * @return void
     */
    public function sendResponse($response) : void;


}