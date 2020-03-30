<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Prototype\Internal;

use Commune\Message\Blueprint\Internal\ShellScope;
use Commune\Support\Arr\ArrayAbleToJson;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShellScope implements ShellScope
{
    use ArrayAbleToJson;

    const PROPERTIES = [
        'chatbotName' => '',
        'shellName' => '',
        'shellChatId' => '',
        'userId' => '',
        'sessionId' => null,
        'sceneId' => '',
        'serverId' => '',
        'traceId' => '',
    ];

    protected $data = [];

    /**
     * IShellScope constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data + self::PROPERTIES;
    }

    public function __get($name)
    {
        if (array_key_exists($name, self::PROPERTIES)) {
            return $this->data[$name]
                ?? self::PROPERTIES[$name];
        }

        return null;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}