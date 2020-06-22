<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\MessengerFaker;

use Commune\Protocals\IntercomMsg;
use Commune\Support\Utils\ArrayUtils;
use Commune\Contracts\Messenger\Condition;
use Commune\Contracts\Messenger\MessageDB;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ArrFakeMessageDB implements MessageDB
{
    public static $maxMessages = 100;

    /**
     * 越新的消息越在前面.
     *
     * @var IntercomMsg[]
     */
    public $messages = [];

    public function recordMessages(
        string $traceId,
        string $fromApp,
        string $fromSession,
        IntercomMsg $input,
        IntercomMsg ...$outputs
    ): void
    {
        array_unshift($this->messages, $input);
        array_unshift($this->messages, ...$outputs);

        ArrayUtils::maxLength($this->messages, self::$maxMessages);
    }


    public function fetch(callable $fetcher): array
    {
        return $fetcher($this);
    }

    public function where(): Condition
    {
        return new ArrFakeCondition($this);
    }

    public function find(string $messageId): ? IntercomMsg
    {
        return $this->messages[$messageId] ?? null;
    }


}