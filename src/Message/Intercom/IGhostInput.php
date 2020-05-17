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

use Commune\Message\Abstracted\IComprehension;
use Commune\Message\Host\Convo\IText;
use Commune\Protocals\Comprehension;
use Commune\Protocals\HostMsg;
use Commune\Protocals\Intercom\GhostInput;
use Commune\Protocals\Intercom\GhostOutput;
use Commune\Protocals\Intercom\ShellMsg;
use Commune\Support\Message\AbsMessage;
use Commune\Support\Struct\Struct;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $cloneId
 * @property string|null $sessionId
 *
 * @property-read string $shellName
 * @property-read string $shellId
 *
 * @property-read string $senderId
 * @property-read string $senderName
 * @property-read string $guestId
 *
 * @property-read string $messageId
 * @property-read string $batchId
 *
 * @property-read HostMsg $message
 * @property-read Comprehension $comprehension
 *
 * @property-read int $deliverAt
 * @property-read int $createdAt
 *
 * @property-read string $sceneId
 * @property-read array $env
 */
class IGhostInput extends AbsMessage implements GhostInput, HasIdGenerator
{
    use IdGeneratorHelper;

    protected $transferNoEmptyRelations = false;

    public function __construct(
        HostMsg $message,
        string $clonerId,
        string $sessionId,
        string $shellName,
        string $shellId,
        string $senderId,
        string $messageId = null,
        array $moreInfo = [
            //'batchId' => 'id',
            //'sceneId' => '',
            //'env' => [],
            //'senderName' => '',
            //'guestId' => '',
            //'deliverAt' => 0,
            //'createdAt' => 0
        ],
        $comprehension = null
    )
    {
        $moreInfo['message'] = $message;
        $moreInfo['cloneId'] = $clonerId;
        $moreInfo['sessionId'] = $sessionId;

        $moreInfo['shellName'] = $shellName;
        $moreInfo['shellId'] = $shellId;

        $moreInfo['senderId'] = $senderId;

        $moreInfo['messageId'] = empty($messageId) ? $this->createUuId() : $messageId;

        $moreInfo['comprehension'] = $comprehension ?? [];
        parent::__construct($moreInfo);
    }

    public static function stub(): array
    {
        return [
            'clonerId' => '',
            'sessionId' => null,

            'shellName' => '',
            'shellId' => '',

            'senderId' => '',
            'senderName' => '',
            'guestId' => '',

            'messageId' => '',
            'batchId' => '',

            'sceneId' => '',
            'env' => [],

            'message' => new IText(),
            'comprehension' => new IComprehension(),

            'deliverAt' => 0,
            'createdAt' => time(),
        ];
    }

    public static function create(array $data = []): Struct
    {
        return new static(
            $data['message'] ?? null,
            $data['cloneId'] ?? '',
            $data['sessionId'] ?? '',
            $data['shellName'] ?? '',
            $data['shellId'] ?? '',
            $data['senderId'] ?? '',
            $data['messageId'] ?? '',
            $data,
            $data['comprehension'] ?? null
        );
    }

    public static function relations(): array
    {
        return [
            'message' => HostMsg::class,
            'comprehension' => IComprehension::class
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
        return round($this->createdAt, 3);
    }

    public function getDeliverAt(): int
    {
        return $this->deliverAt;
    }

    public function getSceneId(): string
    {
        return $this->sceneId;
    }

    public function getEnv(): array
    {
        return $this->env;
    }

    public function getComprehension(): Comprehension
    {
        return $this->comprehension;
    }

    public function getShellName(): string
    {
        return $this->shellName;
    }

    public function getShellId(): string
    {
        return $this->shellId;
    }

    public function senderId(): string
    {
        return $this->senderId;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function getCloneId(): string
    {
        $cloneId = $this->cloneId;
        return empty($cloneId) ? $this->shellId : $cloneId;
    }

    public function getGuestId(): string
    {
        $guestId = $this->guestId;
        return empty($guestId) ? $this->senderId : $guestId;
    }

    public function getSenderId(): string
    {
        return $this->senderId;
    }

    public function getSenderName(): string
    {
        return $this->senderName;
    }


    public function isBroadcasting(): bool
    {
        return $this->message->isBroadcasting();
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function output(
        HostMsg $message,
        int $deliverAt = 0,
        string $cloneId = null,
        string $shellName = null,
        string $guestId = null
    ): GhostOutput
    {
        return new IGhostOutput(
            $message,
            $cloneId ?? $this->cloneId,
            $this->sessionId,
            $shellName ?? $this->shellName,
            $this->shellId,
            $this->senderId,
            $batchId = $this->messageId,
            $guestId ?? $this->guestId,
            null,
            $deliverAt
        );
    }

    public function toShellMsg(): ShellMsg
    {
        return new IShellMsg(
            $this->message,
            $this->shellName,
            $this->shellId,
            $this->senderId,
            $this->messageId,
            $this->deliverAt,
            [
                'sessionId' => $this->sessionId,
                'createdAt' => $this->createdAt,
            ]
        );
    }


}