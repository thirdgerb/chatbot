<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell;

use Commune\Shell\Blueprint\Kernels\RequestKernel;
use Commune\Shell\Blueprint\Shell;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShell implements Shell
{

    /**
     * 生成一个响应请求的内核, 理论上不应该是单例.
     *
     * @return RequestKernel
     */
    public function getReqKernel(): RequestKernel
    {
        return $this->getProcContainer()->get(RequestKernel::class);
    }


}