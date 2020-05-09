<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Memory;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Cloner\ClonerInstance;
use Commune\Blueprint\Ghost\Cloner\ClonerInstanceStub;
use Commune\Support\Arr\ArrayAbleToJson;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RecallStub implements ClonerInstanceStub
{
    use ArrayAbleToJson;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $recallClassName;

    /**
     * RecallStub constructor.
     * @param string $id
     * @param string $recallClassName
     */
    public function __construct(string $id, string $recallClassName)
    {
        $this->id = $id;
        $this->recallClassName = $recallClassName;
    }

    public function toInstance(Cloner $cloner): ClonerInstance
    {
        return call_user_func([$this->recallClassName, 'find'], $cloner);
    }

    public function toArray(): array
    {
        return [
            'type' => static::class,
            'id' => $this->id,
            'recallClassName' => $this->recallClassName,
        ];
    }


}