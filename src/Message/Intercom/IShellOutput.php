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

use Commune\Message\Host\Convo\IVerbalMsg;
use Commune\Protocals\HostMsg;
use Commune\Protocals\Intercom\ShellOutput;
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
class IShellOutput extends AbsMessage implements ShellOutput
{
    use IdGeneratorHelper;

    protected $transferNoEmptyRelations = false;

    public function __construct(
        HostMsg $message,
        string $shellName,
        string $senderId,
        string $messageId = null,
        string $shellId = null,
        array $moreInfo = [
            //'batchId' => 'id',
            //'sessionId' => '',
            //'deliverAt' => 0,
            //'createdAt' => 0
        ]
    )
    {
        $moreInfo['message'] = $message;
        $moreInfo['shellName'] = $shellName;
        $moreInfo['senderId'] = $senderId;

        $moreInfo['messageId'] = empty($messageId) ? $this->createUuId() : $messageId;
        $moreInfo['shellId'] = empty($shellId)
            ? sha1("shellName:$shellName:sender:$senderId")
            : $shellId;

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

            'message' => new IVerbalMsg(),

            'deliverAt' => $now = round(floatval(microtime(true)), 3),
            'createdAt' => $now,
        ];
    }

    public static function create(array $data = []): Struct
    {
        return new static(
            $data['message'] ?? null,
            $data['shellName'] ?? '',
            $data['senderId'] ?? '',
            $data['messageId'] ?? '',
            $data['shellId'] ?? '',
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

    public function getCreatedAt(): float
    {
        return $this->createdAt;
    }

    public function getDeliverAt(): float
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

    public function senderId(): string
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


}