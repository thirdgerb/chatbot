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
class RecollectionStub implements ClonerInstanceStub
{
    use ArrayAbleToJson;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * RecollectionStub constructor.
     * @param string $id
     * @param string $name
     */
    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function toInstance(Cloner $cloner): ClonerInstance
    {
        $def = $cloner->mind->memoryReg()->getDef($this->name);
        return new IRecollection(
            $this->id,
            $def,
            $cloner
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }


}