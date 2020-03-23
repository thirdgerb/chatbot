<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Exceptions;

use Commune\Message\Internal\OutgoingMsg;

/**
 * Messenger 发送同步消息, 可能会抛出的异常.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class MessengerReqException extends \RuntimeException
{
    /**
     * @var OutgoingMsg
     */
    protected $outgoingMsg;

    /**
     * MessengerReqException constructor.
     * @param string $message
     * @param OutgoingMsg $outgoingMsg
     */
    public function __construct(string $message, OutgoingMsg $outgoingMsg)
    {
        $this->outgoingMsg = $outgoingMsg;
        parent::__construct($message);
    }


}