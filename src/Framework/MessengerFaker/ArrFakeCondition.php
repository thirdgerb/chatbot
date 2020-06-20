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
use Commune\Protocals\IntercomMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ArrFakeCondition implements Condition
{

    public function sessionIdIs(string $sessionId): Condition
    {
        // TODO: Implement sessionIdIs() method.
    }

    public function traceIdIs(string $batchId): Condition
    {
        // TODO: Implement batchIdIs() method.
    }

    public function guestIdIs(string $guestId): Condition
    {
        // TODO: Implement guestIdIs() method.
    }

    public function isDeliverableAfter(float $time): Condition
    {
        // TODO: Implement deliverAfter() method.
    }

    public function isCreatedAfter(float $time): Condition
    {
        // TODO: Implement createdAfter() method.
    }

    public function afterId(string $messageId): Condition
    {
        // TODO: Implement afterId() method.
    }

    public function get(): array
    {
        // TODO: Implement get() method.
    }

    public function first(): IntercomMsg
    {
        // TODO: Implement first() method.
    }

    public function count(): int
    {
        // TODO: Implement count() method.
    }

    public function range(int $offset, int $limit): array
    {
        // TODO: Implement range() method.
    }


}