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
use Commune\Protocals\Intercom\InputMsg;
use Commune\Protocals\Intercom\OutputMsg;
use Commune\Support\Struct\Struct;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property string $shellName
 * @property string $sceneId
 * @property array $env
 */
class IInputMsg extends AIntercomMsg implements InputMsg
{

    protected $transferNoEmptyRelations = false;

    protected $transferNoEmptyData = true;

    public function __construct(
        string $shellName,
        HostMsg $message,
        string $guestId,
        string $messageId = null,
        string $convoId = null,
        string $sessionId = null,
        string $guestName = null,
        int $deliverAt = 0,
        string $sceneId = '',
        array $env = [],
        Comprehension $comprehension = null
    )
    {
        $data = [
            'shellName' => $shellName,
            'messageId' => empty($messageId)
                ? $this->createUuId()
                : $messageId,
            'traceId' => '',
            'sessionId' => $sessionId ?? '',
            'convoId' => $convoId ?? '',
            'guestId' => $guestId,
            'guestName' => $guestName ?? '',
            'message' => $message,
            'deliverAt' => $deliverAt,
            'createdAt' => time(),
            'sceneId' => $sceneId,
            'env' => $env,
            'comprehension' => $comprehension ?? new IComprehension()

        ];
        parent::__construct($data);
    }


    public static function stub(): array
    {
        return [
            'shellName' => 'demo',
            'messageId' => '',
            'traceId' => '',
            'sessionId' => '',
            'convoId' => '',
            'guestId' => '',
            'guestName' => '',
            'message' => new IText(),
            'deliverAt' => 0,
            'createdAt' => time(),
            'sceneId' => '',
            'env' => [],
            'comprehension' => new IComprehension(),
        ];
    }

    public static function create(array $data = []): Struct
    {
        return new static(
            $data['shellName'],
            $data['message'],
            $data['guestId'] ?? '',
            $data['messageId'] ?? null,
            $data['convoId'] ?? null,
            $data['sessionId'] ?? null,
            $data['guestName'] ?? null,
            $data['deliverAt'] ?? 0,
            $data['sceneId'] ?? '',
            $data['env'] ?? [],
            $data['comprehension'] ?? null
        );
    }

    public static function relations(): array
    {
        return [
            'comprehension' => IComprehension::class,
            'message' => HostMsg::class,
        ];
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

    public function output(
        HostMsg $message,
        int $deliverAt = 0,
        string $guestId = null,
        string $sessionId = null,
        string $messageId = null
    ): OutputMsg
    {
        return new IOutputMsg(
            $message,
            $this->getTraceId(),
            $guestId ?? $this->guestId,
            $messageId,
            $this->convoId,
            $sessionId ?? $this->sessionId,
            $this->guestName,
            $deliverAt
        );
    }

    public function getShellName(): string
    {
        return $this->shellName;
    }

    public function setSceneId(string $sceneId): void
    {
        $this->sceneId = $sceneId;
    }


}