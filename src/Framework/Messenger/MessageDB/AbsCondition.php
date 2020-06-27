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
use Commune\Contracts\Messenger\MessageDB;
use Commune\Protocals\IntercomMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsCondition implements Condition
{
    /**
     * @var MessageDB
     */
    public  $db;

    /*------ 临时规则 -------*/

    /**
     * @var null|string
     */
    public  $batchId = null;

    /**
     * @var null|string
     */
    public  $sessionId = null;

    /**
     * @var null|string
     */
    public  $creatorId = null;

    /**
     * @var null|int
     */
    public  $deliverAfter = null;

    /**
     * @var null|int
     */
    public  $createdAfter = null;

    /**
     * @var null|string
     */
    public  $idAfter = null;

    /**
     * @var int
     */
    public  $offset = 0;

    /**
     * @var null|int
     */
    public  $limit = null;

    /**
     * AbsCondition constructor.
     * @param MessageDB $db
     */
    public function __construct(MessageDB $db)
    {
        $this->db = $db;
    }


    public function batchIs(string $batchId): Condition
    {
        $this->batchId = $batchId;
        return $this;
    }

    public function sessionIs(string $sessionId): Condition
    {
        $this->sessionId = $sessionId;
        return $this;
    }


    public function creatorIs(string $creatorId): Condition
    {
        $this->creatorId = $creatorId;
        return $this;
    }

    public function deliverableAfter(int $time): Condition
    {
        $this->deliverAfter = $time;
        return $this;
    }

    public function createdAfter(int $time): Condition
    {
        $this->createdAfter = $time;
        return $this;
    }

    public function afterId(string $messageId): Condition
    {
        $this->idAfter = $messageId;
        return $this;
    }


    public function get(): array
    {
        return $this->db->fetch($this);
    }

    public function range(int $offset, int $limit): array
    {
        $this->offset = $offset;
        $this->limit = $limit;
        return $this->get();
    }

    public function first(): ? IntercomMsg
    {
        return $this->range(0, 1)[0] ?? null;
    }


    /**
     * @param MessageDB $db
     * @return IntercomMsg[]
     */
    abstract public function __invoke(MessageDB $db) : array;

}