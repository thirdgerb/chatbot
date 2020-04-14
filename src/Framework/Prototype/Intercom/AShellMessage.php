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

use Commune\Framework\Blueprint\Intercom\ShellMessage;
use Commune\Framework\Blueprint\Intercom\ShellOutput;
use Commune\Framework\Blueprint\Intercom\ShellScope;
use Commune\Message\Blueprint\Message;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Babel\BabelSerializable;
use Commune\Support\Babel\TBabelSerializable;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AShellMessage implements ShellMessage, HasIdGenerator
{
    use ArrayAbleToJson, TBabelSerializable, IdGeneratorHelper;

    const PROPERTIES = [
        'message' => 'msg',
        'scope' => 'scp',
    ];

    /**
     * @var Message
     */
    protected $msg;

    /**
     * @var ShellScope
     */
    protected $scp;

    /**
     * AShellMsg constructor.
     * @param Message $msg
     * @param ShellScope $scp
     */
    public function __construct(
        Message $msg,
        ShellScope $scp
    )
    {
        $this->msg = $msg;
        $this->scp = $scp;
    }

    public function replace(Message $msg): void
    {
        $this->msg = $msg;
    }


    public function derive(Message $msg) : ShellOutput
    {
        return new IShellOutput($msg, $this->scp);
    }


    public function toArray(): array
    {
        return [
            'msg' => $this->msg->toArray(),
            'scp' => $this->scp->toArray(),
        ];
    }

    public function __sleep(): array
    {
        return [
            'msg',
            'scp',
        ];
    }

    public static function createNewSerializable(array $input): ? BabelSerializable
    {
        return new static(
            $input['msg'],
            $input['scp']
        );
    }

    public function __get($name)
    {
        $property = static::PROPERTIES[$name] ?? '';
        if (!empty($property)) {
            return $this->{$property};
        }
        return null;
    }

}