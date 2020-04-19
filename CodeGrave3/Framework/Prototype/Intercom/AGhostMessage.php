<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Prototype\Intercom;

use Commune\Framework\Blueprint\Intercom\GhostMessage;
use Commune\Framework\Blueprint\Intercom\ShellMessage;
use Commune\Message\Blueprint\Message;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Support\Babel\TBabelSerializable;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AGhostMessage implements GhostMessage, HasIdGenerator
{
    use ArrayAbleToJson, TBabelSerializable, IdGeneratorHelper;

    const PROPERTIES = [
        'chatId' => 'cid',
        'shellName' => 'shn',
        'shellMessage' => 'shm',
        'traceId' => 'tid',
        'messageId' => 'mid',
    ];

    /**
     * @var string
     */
    protected $cid;

    /**
     * @var string
     */
    protected $shn;

    /**
     * @var ShellMessage
     */
    protected $shm;

    /**
     * @var string
     */
    protected $tid;

    /**
     * @var string
     */
    protected $mid;

    public function __construct(
        string $shellName,
        string $cid,
        ShellMessage $shellMessage,
        string $traceId,
        string $messageId = null
    )
    {
        $this->shellName = $shellName;
        $this->cid = $cid;
        $this->shellMessage = $shellMessage;
        $this->tid = $traceId;
        $this->mid = $messageId ?? $this->createUuId();
    }

    public function getMessage(): Message
    {
        return $this->shellMessage->message;
    }

    public function getUserId(): string
    {
        return $this->shellMessage->scope->userId;
    }

    public function getSessionId(): ? string
    {
        return $this->shellMessage->scope->sessionId;
    }

    public function getSceneId(): string
    {
        return $this->shellMessage->scope->sceneId;
    }

    public function getChatbotName(): string
    {
        return $this->shellMessage->scope->chatbotName;
    }


    public function toArray(): array
    {
        $fields = $this->__sleep();
        $data = [];
        foreach ($fields as $field) {
            $value = $this->{$field};
            if ($value instanceof ArrayAndJsonAble) {
                $value = $value->toArray();
            }

            $data[$field] = $value;
        }

        return $data;
    }

    public function replace(Message $message): void
    {
        $this->shellMessage->replace($message);
    }


    public function derive(Message $message, array $shellChatIds, int $deliverAt = null): array
    {
        $outputs = [];
        foreach ($shellChatIds as $cid => $shell) {
            $output = new IGhostOutput(
                $shell,
                $cid,
                $this->shellMessage->derive($message),
                $this->tid,
                $deliverAt
            );

            $outputs[] = $output;
        }

        return $outputs;
    }


    public function __get($name)
    {
        $propertyName = static::PROPERTIES[$name] ?? '';

        if (!empty($propertyName)) {
            return $this->{$propertyName};
        }

        return null;
    }

    public function __sleep(): array
    {
        return [
            'shn',
            'cid',
            'shm',
            'mid',
            'tid',
        ];
    }
}