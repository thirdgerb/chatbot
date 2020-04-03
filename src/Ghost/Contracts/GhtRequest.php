<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Contracts;

use Commune\Framework\Blueprint\Intercom\GhostInput;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GhtRequest
{

    /**
     * 检查请求是否合法
     * @return bool
     */
    public function validate() : bool;

    /**
     * @return GhostInput
     */
    public function getInput() : GhostInput;

    /**
     * @return string
     */
    public function getBrief() : string;
}