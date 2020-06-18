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
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GuzzlePsr7Packer extends Packer
{

    /**
     * @return Request
     */
    public function getRequest() : Request;

    /**
     * @param Response $response
     * @return void
     */
    public function sendResponse($response) : void;

}