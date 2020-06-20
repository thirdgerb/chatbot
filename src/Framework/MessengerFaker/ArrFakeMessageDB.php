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


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ArrFakeMessageDB implements MessageDB
{
    public function recordBatch(InputMsg $input, OutputMsg ...$outputs): void
    {
        // TODO: Implement recordBatch() method.
    }

    public function fetch(callable $fetcher): array
    {
        // TODO: Implement fetch() method.
    }

    public function where(): Condition
    {
        // TODO: Implement where() method.
    }

    public function find(string $messageId): ? IntercomMsg
    {
        // TODO: Implement find() method.
    }


}