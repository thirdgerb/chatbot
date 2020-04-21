<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Platform\Ghost;

use Commune\Protocals\Intercom\GhostInput;
use Commune\Protocals\Intercom\GhostMsg;


/**
 * Ghost 的请求
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GhostReq
{

    public function setInput(GhostInput $input) : void;

    public function getInput() : GhostInput;

    /**
     * 请求不合法, 拒绝.
     * @return GhostRes
     */
    public function reject() : GhostRes;

    /**
     * 服务端错误.
     * @param int $errcode
     * @param string $errmsg
     * @return GhostRes
     */
    public function fail(int $errcode, string $errmsg) : GhostRes;

    /**
     * 设置 Ghost 的响应.
     * @param GhostMsg[] $messages
     * @return GhostRes
     */
    public function response(array $messages) : GhostRes;
}