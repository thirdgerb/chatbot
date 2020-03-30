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

use Commune\Message\Blueprint\Internal\InternalMsg;
use Commune\Message\Blueprint\Internal\ShellScope;
use Commune\Message\Blueprint\Message;
use Commune\Support\Arr\ArrayAbleToJson;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AInternalMsg implements InternalMsg
{
    use ArrayAbleToJson;

    /**
     * @var Message
     */
    protected $message;

    /**
     * @var ShellScope
     */
    protected $scope;

    /**
     * IInternalMsg constructor.
     * @param Message $message
     * @param ShellScope $scope
     */
    public function __construct(Message $message, ShellScope $scope)
    {
        $this->message = $message;
        $this->scope = $scope;
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }
        return null;
    }

}