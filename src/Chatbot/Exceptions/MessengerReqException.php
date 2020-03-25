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

use Commune\Message\Internal\OutputMsg;

/**
 * Messenger 发送同步消息, 可能会抛出的异常.
 * 该异常携带一个消息作为返回.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class MessengerReqException extends \RuntimeException
{
    /**
     * @var OutputMsg
     */
    protected $outgoingMsg;

    /**
     * MessengerReqException constructor.
     * @param string $message
     * @param OutputMsg $outgoingMsg
     */
    public function __construct(string $message, OutputMsg $outgoingMsg)
    {
        $this->outgoingMsg = $outgoingMsg;
        parent::__construct($message);
    }

    /**
     * @return OutputMsg
     */
    public function getOutgoingMsg(): OutputMsg
    {
        return $this->outgoingMsg;
    }
}