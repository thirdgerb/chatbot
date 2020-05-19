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

use Commune\Message\Host\Convo\IText;
use Commune\Protocals\HostMsg;
use Commune\Protocals\Intercom\ShellMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Support\Struct\Struct;
use Commune\Support\Uuid\IdGeneratorHelper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $shellName
 * @property-read string $shellId
 * @property-read string $senderId
 *
 * @property-read string $messageId
 * @property-read string $batchId
 * @property-read string|null $sessionId
 *
 * @property-read HostMsg $message
 *
 * @property-read float $deliverAt
 * @property-read float $createdAt
 *
 */
class IShellMsg extends AbsMessage implements ShellMsg
{
    use IdGeneratorHelper;

    protected $transferNoEmptyRelations = false;

    public function __construct(
        HostMsg $message,
        string $shellName,
        string $shellId,
        string $senderId,
        string $messageId = null,
        float $deliverAt = 0,
        array $moreInfo = [
            //'sessionId' => '',
            //'createdAt' => 0
        ]
    )
    {
        $moreInfo['message'] = $message;
        $moreInfo['shellName'] = $shellName;
        $moreInfo['shellId'] = $shellId;
        $moreInfo['senderId'] = $senderId;
        $moreInfo['messageId'] = $messageId ?? $this->createUuId();
        $moreInfo['deliverAt'] = $deliverAt;

        parent::__construct($moreInfo);
    }

    public static function stub(): array
    {
        return [
            'shellName' => '',
            'senderId' => '',
            'shellId' => '',

            'messageId' => '',
            'batchId' => '',

            'message' => new IText(),

            'deliverAt' => 0,
            'createdAt' => time(),
        ];
    }

    public static function create(array $data = []): Struct
    {
        return new static(
            $data['message'] ?? null,
            $data['shellName'] ?? '',
            $data['shellId'] ?? '',
            $data['senderId'] ?? '',
            $data['messageId'] ?? '',
            $data['deliverAt'] ?? 0,
            $data
        );
    }

    public static function relations(): array
    {
        return [
            'message' => HostMsg::class,
        ];
    }

    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function getBatchId(): string
    {
        $batchId = $this->batchId;
        return empty($batchId) ? $this->messageId : $this->batchId;
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

    public function isEmpty(): bool
    {
        return false;
    }

    public function getShellId(): string
    {
        return $this->shellId;
    }

    public function getSenderId(): string
    {
        return $this->senderId;
    }

    public function getSessionId(): ? string
    {
        return $this->sessionId;
    }

    public function getShellName(): string
    {
        return $this->shellName;
    }

    public function getText(): string
    {
        return $this->toPrettyJson();
    }


}