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
 * @property string $messageId  为空则自动生成.
 * @property string $shellName
 * @property string $traceId    允许为空
 * @property string $sessionId  会话Id, 为空则是 guestId
 * @property string $convoId    多轮会话的 ID. 允许为空. 除非客户端有指定的 conversation.
 * @property string $guestId    用户的ID. 不可以为空.
 * @property string $guestName  用户的姓名. 可以为空.
 * @property HostMsg $message   输入消息. 不可以为空.
 * @property int $createdAt     创建时间.
 * @property int $deliverAt     发送时间. 默认为0.
 *
 */
abstract class AIntercomMsg extends AbsMessage implements IntercomMsg, HasIdGenerator
{
    use IdGeneratorHelper;

    /**
     * @var string
     */
    protected $_normalizedText;

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

    public function getProtocalId(): string
    {
        return $this->getMessage()->getProtocalId();
    }


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


    public function setMessage(HostMsg $message): void
    {
        $this->message = $message;
    }


    public function divide(
        HostMsg $message = null,
        string $shellName = null,
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
                $message->__set($key, $val);
            }
        }

        if (is_null($message)) {
            $message->message = clone $message->message;
        } else {
            $message->messageId = $this->createUuId();
        }

        return $message;
    }

    public function isMsgType(string $hostMessageType): bool
    {
        return is_a($this->message, $hostMessageType, TRUE);
    }

    public function getMsgRenderId(string $renderId): string
    {
        return $this->message->getProtocalId();
    }

    public function getMsgText(): string
    {
        return $this->message->getText();
    }

    public function getNormalizedText(): string
    {
        return $this->_normalizedText
            ?? $this->_normalizedText = StringUtils::normalizeString($this->getMsgText());
    }

    public function setConvoId(string $convoId): void
    {
        $this->convoId = $convoId;
    }


    public function __set_messageId(string $name, string $value) : void
    {
        $this->_data[$name] = empty($value)
            ? $this->createUuId()
            : $value;
    }


}