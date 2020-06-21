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

use Commune\Contracts\Messenger\Condition;
use Commune\Contracts\Messenger\MessageDB;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Protocals\Intercom\OutputMsg;
use Commune\Protocals\IntercomMsg;
use Commune\Support\Utils\ArrayUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ArrFakeMessageDB implements MessageDB
{
    public static $maxBatch = 10;

    public static $maxMessages = 100;

    public $batches = [];

    public $messages = [];

    public function recordMessages(
        string $traceId,
        InputMsg $input,
        OutputMsg ...$outputs
    ): void
    {
        $this->batches[$traceId] = [$input, $outputs];

        $this->messages[$input->getMessageId()] = $input;

        foreach ($outputs as $output) {
            $this->messages[$output->getMessageId()] = $output;
        }

        ArrayUtils::maxLength($this->batches, self::$maxBatch);
        ArrayUtils::maxLength($this->messages, self::$maxMessages);
    }


    public function fetch(callable $fetcher): array
    {
        return $fetcher($this);
    }

    public function where(): Condition
    {
        // TODO: Implement where() method.
    }

    public function find(string $messageId): ? IntercomMsg
    {
        return $this->messages[$messageId] ?? null;
    }


}