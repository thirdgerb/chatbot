<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype;

use Commune\Container\ContainerContract;
use Commune\Framework\Blueprint\ReqContainer;
use Commune\Framework\Prototype\TReqContainer;
use Commune\Support\RunningSpy\Spied;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IGhostReqContainer implements ReqContainer, Spied
{
    use TReqContainer;

    public function __construct(ContainerContract $parentContainer)
    {
        $this->parentContainer = $parentContainer;
    }
}