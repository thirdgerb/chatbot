<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost;

use Commune\Blueprint\Platform\Ghost\GhostReq;
use Commune\Blueprint\Platform\Ghost\GhostRes;
use Commune\Protocals\Intercom\GhostInput;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GhostKernel
{
    /**
     * 创建一个新克隆
     *
     * @param GhostInput $input
     * @return Cloner
     */
    public function newCloner(GhostInput $input) : Cloner;

    public function onMessage(GhostReq $req) : GhostRes;

}