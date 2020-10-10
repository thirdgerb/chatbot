<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Messenger\MessageDB;

use Commune\Contracts\Messenger\Condition;
use Commune\Protocols\IntercomMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CacheOnlyMessageDB extends AbsMessageDB
{

    public function saveBatchMessages(
        string $traceId,
        string $fromApp,
        string $fromSession,
        string $batchId,
        IntercomMsg ...$outputs
    ): void
    {
        return;
    }

    public function loadBatchMessages(string $batchId): array
    {
        return [];
    }

    public function where(): Condition
    {
        return new FakeCondition($this);
    }

    public function find(string $messageId): ? IntercomMsg
    {
        return null;
    }


}