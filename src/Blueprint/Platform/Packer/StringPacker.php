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
 * 通常用于 TCP, 或者命令行. 输入输出都会解析成字符串.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface StringPacker extends Packer
{

    public function getRequest() : string;

    /**
     * @param string $response
     */
    public function sendResponse($response): void;


}