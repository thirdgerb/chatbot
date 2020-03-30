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

use Commune\Message\Blueprint\Abstracted\Comprehension;
use Commune\Message\Blueprint\Internal\InputMsg;
use Commune\Message\Blueprint\Internal\ShellScope;
use Commune\Message\Blueprint\Message;
use Commune\Message\Prototype\Abstracted\IComprehension;
use Commune\Support\Babel\TSerializable;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IInputMsg extends AInternalMsg implements InputMsg
{
    use TSerializable;

    /**
     * @var string
     */
    protected $sceneId;

    /**
     * @var array
     */
    protected $sceneEnv;

    /**
     * @var Comprehension
     */
    protected $comprehension;

    public function __construct(
        Message $message,
        ShellScope $scope,
        string $sceneId = '',
        array $sceneEnv = [],
        Comprehension $comprehension = null)
    {
        parent::__construct($message, $scope);
        $this->sceneId = $sceneId;
        $this->sceneEnv = $sceneEnv;
        $this->comprehension = $comprehension ?? new IComprehension();
    }


    public function toArray(): array
    {
        return [
            'message' => $this->message->toArray(),
            'scope' => $this->scope->toArray(),
            'sceneId' => $this->sceneId,
            'sceneEnv' => $this->sceneEnv,
            'comprehension' => $this->comprehension->toArray(),
        ];
    }

    public function __sleep(): array
    {
        return [
            'message',
            'scope',
            'sceneId',
            'sceneEnv',
            'comprehension'
        ];
    }


}