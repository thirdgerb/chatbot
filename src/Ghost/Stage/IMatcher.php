<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Stage;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Routing\Matcher;
use Commune\Protocals\HostMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IMatcher implements Matcher
{

    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * @var HostMsg
     */
    protected $message;

    /**
     * IMatcher constructor.
     * @param Cloner $cloner
     * @param HostMsg $message
     */
    public function __construct(Cloner $cloner, HostMsg $message)
    {
        $this->cloner = $cloner;
        $this->message = $message;
    }


}