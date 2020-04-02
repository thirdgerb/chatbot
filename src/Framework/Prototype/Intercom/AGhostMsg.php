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

    /**
     * @var string
     */
    protected $chatId;

    /**
     * @var string
     */
    protected $shellName;

    /**
     * @var ShellMsg
     */
    protected $shellMessage;

    /**
     * @var string
     */
    protected $messageId;

    public function __construct(
        string $shellName,
        string $chatId,
        ShellMsg $shellMessage,
        string $messageId = null
    )
    {
        $this->shellName = $shellName;
        $this->chatId = $chatId;
        $this->shellMessage = $shellMessage;
        $this->messageId = $messageId ?? $this->createUuId();
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

    public function derive(Message $message, array $shellChatIds, int $deliverAt = null): array
    {
        $outputs = [];
        foreach ($shellChatIds as $shell => $chatId) {
            $output = new IGhostOutput(
                $shell,
                $chatId,
                $this->shellMessage->derive($message),
                $deliverAt
            );

            $outputs[] = $output;
        }

        return $outputs;
    }


    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }
        return null;
    }

    public function __sleep(): array
    {
        return [
            'shellName',
            'chatId',
            'messageId',
            'shellMessage',
        ];
    }
}