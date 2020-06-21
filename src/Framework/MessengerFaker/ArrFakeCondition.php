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
    /**
     * @var ArrFakeMessageDB
     */
    protected $db;

    /*------ 临时规则 -------*/

    protected $traceId = null;

    protected $sessionId = null;

    protected $guestId = null;

    /**
     * @var null|int
     */
    protected $deliverAt = null;

    /**
     * @var null|int
     */
    protected $createdAfter = null;

    /**
     * @var null
     */
    protected $idAfter = null;

    /**
     * ArrFakeCondition constructor.
     * @param ArrFakeMessageDB $db
     */
    public function __construct(ArrFakeMessageDB $db)
    {
        $this->db = $db;
    }


    public function traceIdIs(string $batchId): Condition
    {
        // TODO: Implement traceIdIs() method.
    }


    public function sessionIdIs(string $sessionId): Condition
    {
        // TODO: Implement sessionIdIs() method.
    }


    public function guestIdIs(string $guestId): Condition
    {
        // TODO: Implement guestIdIs() method.
    }

    public function isDeliverableAfter(int $time): Condition
    {
        // TODO: Implement isDeliverableAfter() method.
    }

    public function isCreatedAfter(int $time): Condition
    {
        // TODO: Implement isCreatedAfter() method.
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

    /**
     * @param array $message
     * @return IntercomMsg[]
     */
    public function __invoke(array $message) : array
    {
    }


}