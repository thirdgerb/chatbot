<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Intercom;

use Commune\Protocals\HostMsg;
use Commune\Protocals\IntercomMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Support\Utils\StringUtils;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property string $messageId
 * @property string $traceId
 * @property string $sessionId
 * @property string $convoId
 * @property string $guestId
 * @property string $guestName
 * @property HostMsg $message
 * @property int $createdAt
 * @property int $deliverAt
 *
 */
abstract class AIntercomMsg extends AbsMessage implements IntercomMsg, HasIdGenerator
{
    use IdGeneratorHelper;

    public static function relations(): array
    {
        return [
            'message' => HostMsg::class
        ];
    }

    public function isEmpty(): bool
    {
        return false;
    }

    /*------- properties -------*/

    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function getTraceId(): string
    {
        $traceId = $this->traceId;

        return empty($traceId)
            ? $this->messageId
            : $traceId;
    }

    public function getSessionId(): string
    {
        $sessionId = $this->sessionId;
        return empty($sessionId)
            ? $this->guestId
            : $sessionId;
    }

    public function getConversationId(): string
    {
        return $this->convoId;
    }

    public function getGuestId(): string
    {
        return $this->guestId;
    }

    public function getGuestName(): string
    {
        return $this->guestName;
    }

    public function getMessage(): HostMsg
    {
        return $this->message;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function getDeliverAt(): int
    {
        return $this->deliverAt;
    }


    public function replaceMsg(HostMsg $message): void
    {
        $this->message = $message;
    }


    public function divide(
        HostMsg $message = null,
        string $sessionId = null,
        string $convoId = null,
        string $guestId = null,
        string $guestName = null,
        int $deliverAt = null
    ): IntercomMsg
    {
        $map = get_defined_vars();

        $message = clone $this;
        foreach ($map as $key => $val) {
            if (isset($val)) {
                $message->{$key} = $val;
            }
        }
        return $message;
    }

    public function isMsgType(string $hostMessageType): bool
    {
        return is_a($this->message, $hostMessageType, TRUE);
    }

    public function getMsgRenderId(string $renderId): string
    {
        return $this->message->getRenderId();
    }

    public function getMsgText(): string
    {
        return $this->message->getText();
    }


}