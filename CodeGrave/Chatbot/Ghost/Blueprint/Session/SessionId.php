<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Ghost\Blueprint\Session;


/**
 * SessionId 的对象. 用于依赖注入.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $sessionId
 * @property-read string $chatId
 */
class SessionId
{
    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var string
     */
    protected $chatId;

    /**
     * SessionId constructor.
     * @param $sessionId
     * @param $chatId
     */
    public function __construct(string $chatId, string $sessionId)
    {
        $this->sessionId = $sessionId;
        $this->chatId = $chatId;
    }


    public function __get($name)
    {
        return $this->{$name};
    }

}