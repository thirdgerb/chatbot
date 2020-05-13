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
     * @var bool
     */
    protected $longTerm;

    /**
     * RecollectionStub constructor.
     * @param string $id
     * @param string $name
     * @param bool $longTerm
     */
    public function __construct(string $id, string $name, bool $longTerm)
    {
        $this->id = $id;
        $this->name = $name;
        $this->longTerm = $longTerm;
    }

    public function toInstance(Cloner $cloner): ClonerInstance
    {
        $def = $cloner->mind->memoryReg()->getDef($this->name);

        $defaults = $def->getDefaults();
        $memory = $cloner->runtime->findMemory(
            $this->id,
            $this->longTerm,
            $defaults
        );

        return new IRecollection(
            $this->id,
            $this->name,
            $this->longTerm,
            $memory,
            $cloner
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'longTerm' => $this->longTerm
        ];
    }


}