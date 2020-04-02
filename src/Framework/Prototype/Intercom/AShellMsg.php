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

use Commune\Framework\Blueprint\Intercom\ShellMsg;
use Commune\Framework\Blueprint\Intercom\ShellOutput;
use Commune\Framework\Blueprint\Intercom\ShellScope;
use Commune\Message\Blueprint\Message;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Babel\TBabelSerializable;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AShellMsg implements ShellMsg, HasIdGenerator
{
    use ArrayAbleToJson, TBabelSerializable, IdGeneratorHelper;

    /**
     * @var Message
     */
    protected $message;

    /**
     * @var ShellScope
     */
    protected $scope;

    /**
     * AShellMsg constructor.
     * @param Message $message
     * @param ShellScope $scope
     */
    public function __construct(
        Message $message,
        ShellScope $scope
    )
    {
        $this->message = $message;
        $this->scope = $scope;
    }

    public function derive(Message $message) : ShellOutput
    {
        return new IShellOutput($message, $this->scope);
    }


    public function toArray(): array
    {
        return [
            'message' => $this->message->toArray(),
            'scope' => $this->scope->toArray(),
        ];
    }

    public function __sleep(): array
    {
        return [
            'message',
            'scope',
        ];
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }
        return null;
    }

}