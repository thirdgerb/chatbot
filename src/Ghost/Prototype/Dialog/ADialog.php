<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Dialog;

use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Stage;
use Commune\Support\RunningSpy\Spied;
use Commune\Support\RunningSpy\SpyTrait;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ADialog implements Dialog, Spied
{
    use SpyTrait;

    protected $uuid;

    /**
     * @var Conversation
     */
    protected $conversation;

    /**
     * ADialog constructor.
     * @param Conversation $conversation
     */
    public function __construct(Conversation $conversation)
    {
        $this->conversation = $conversation;
        $this->uuid = $conversation->getUuid();
        static::addRunningTrace($this->uuid, $this->uuid);
    }

    public function __destruct()
    {
        static::removeRunningTrace($this->uuid);
        $this->conversation = null;
    }

}