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

use Commune\Framework\Blueprint\Intercom\GhostMsg;
use Commune\Framework\Blueprint\Intercom\ShellMsg;
use Commune\Message\Blueprint\Message;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Support\Babel\TBabelSerializable;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AGhostMsg implements GhostMsg, HasIdGenerator
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
     * @var ShellMsg
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
        ShellMsg $shellMessage,
        string $traceId,
        string $messageId = null
    )
    {
        $this->shn = $shellName;
        $this->cid = $cid;
        $this->shm = $shellMessage;
        $this->tid = $traceId;
        $this->mid = $messageId ?? $this->createUuId();
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
        $this->shm->replace($message);
    }


    public function derive(Message $message, array $shellcids, int $deliverAt = null): array
    {
        $outputs = [];
        foreach ($shellcids as $shell => $cid) {
            $output = new IGhostOutput(
                $shell,
                $cid,
                $this->shm->derive($message),
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