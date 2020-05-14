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
use Commune\Blueprint\Ghost\Cloner\ClonerInstanceStub;
use Commune\Blueprint\Ghost\Cloner\ClonerScope;
use Commune\Blueprint\Ghost\Context\ParamBuilder;
use Commune\Blueprint\Ghost\Memory\Recall;
use Commune\Blueprint\Ghost\MindDef\MemoryDef;
use Commune\Blueprint\Ghost\MindMeta\MemoryMeta;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Arr\TArrayAccessToMutator;

/**
 * 多轮对话的记忆单元.
 * 根据 Scopes 决定是长程的还是短程(session 级别)的.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class RecallPrototype implements Recall
{
    use ArrayAbleToJson, TArrayAccessToMutator, TRecollection;

    private function __construct(string $id, MemoryDef $def, Cloner $cloner)
    {
        $this->_id = $id;
        $this->_def = $def;
        $this->_cloner = $cloner;
    }


    public function getName(): string
    {
        return static::recallName();
    }


    abstract public static function recallName() : string;

    /**
     * @param Cloner $cloner
     * @param string|null $id
     * @return static
     */
    public static function find(Cloner $cloner, string $id = null) : Recall
    {
        $def = static::getMemoryDef($cloner);
        return new static(
            $id ?? $def->makeScopeId($cloner),
            $def,
            $cloner
        );
    }

    protected static function getMemoryDef(Cloner $cloner) : MemoryDef
    {
        $name = static::recallName();
        $memoryReg = $cloner->mind->memoryReg();
        if (!$memoryReg->hasDef($name)) {

            $builder = static::getParamOptions($builder);
            $memoryMeta = new MemoryMeta([
                'name' => $name,
                'scopes' => static::getScopes(),
                'params' => $builder->toParamOptions(),
            ]);

            $memoryReg->registerDef($memoryMeta->getWrapper());
        }
        return $memoryReg->getDef($name);
    }

    public function toInstanceStub(): ClonerInstanceStub
    {
        return new RecallStub($this->_id, static::class);
    }


    private function __clone()
    {
    }

}