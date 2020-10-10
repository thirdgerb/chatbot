<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Messenger\Fake;

use Commune\Contracts\Messenger\Condition;
use Commune\Protocols\IntercomMsg;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ArrCondition implements Condition
{
    /**
     * @var ArrMessageDB
     */
    protected $db;

    /*------ 临时规则 -------*/

    protected $batchId = null;

    protected $sessionId = null;

    protected $guestId = null;

    /**
     * @var null|int
     */
    protected $deliverAfter = null;

    /**
     * @var null|int
     */
    protected $createdAfter = null;

    /**
     * @var null|string
     */
    protected $idAfter = null;

    /**
     * ArrFakeCondition constructor.
     * @param ArrMessageDB $db
     */
    public function __construct(ArrMessageDB $db)
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
        $this->guestId = $creatorId;
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
        $messages = $this->db->fetch(function(ArrMessageDB $db) {

            $selections = [];
            foreach ($db->messages as $message) {

                if (isset($this->batchId) && $message->getBatchId() !== $this->batchId) {
                    continue;
                }

                if (isset($this->sessionId) && $message->getSessionId() !== $this->sessionId) {
                    continue;
                }

                if (isset($this->guestId) && $message->getCreatorId() !== $this->guestId) {
                    continue;
                }

                if (
                    isset($this->deliverAfter)
                    && $this->deliverAfter > $message->getDeliverAt()
                ) {
                    continue;
                }

                if (
                    isset($this->createdAfter)
                    && $this->createdAfter > $message->getCreatedAt()
                ) {
                    continue;
                }

                if (
                    isset($this->idAfter)
                    && StringUtils::isStrGreaterThen(
                        $this->idAfter,
                        $message->getMessageId()
                    )
                ) {
                    continue;
                }

                $selections[] = $message;
            }

            return $selections;
        });

        // 输出的消息倒过来排序.
        return array_reverse($messages);
    }

    public function first(): IntercomMsg
    {
        return $this->get()[0] ?? null;
    }

    public function count(): int
    {
        $messages = $this->get();
        return count($messages);
    }

    public function range(int $offset, int $limit): array
    {
        $messages = $this->get();
        return array_slice($messages, $offset, $limit);
    }
}