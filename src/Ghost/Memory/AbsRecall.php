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
use Commune\Blueprint\Ghost\MindSelfRegister;
use Commune\Blueprint\Ghost\Mindset;
use Commune\Ghost\Support\ContextUtils;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Blueprint\Ghost\Memory\Recall;
use Commune\Support\Arr\TArrayAccessToMutator;
use Commune\Blueprint\Ghost\MindDef\MemoryDef;
use Commune\Blueprint\Ghost\MindMeta\MemoryMeta;
use Commune\Blueprint\Ghost\Cloner\ClonerInstanceStub;

/**
 * 不受多轮对话限制的记忆单元
 * 根据 Scopes 决定是长程的还是短程(session 级别)的.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsRecall implements Recall, MindSelfRegister
{
    use ArrayAbleToJson, TArrayAccessToMutator, TRecollection;

    private function __construct(string $id, MemoryDef $def, Cloner $cloner)
    {
        $this->_id = $id;
        $this->_def = $def;
        $this->_cloner = $cloner;
        $this->_memory = $def->recall($cloner, $id);
    }

    /**
     * 定义记忆体的作用域
     *
     * @see Cloner\ClonerScope
     * @return string[]
     */
    abstract public static function __scopes() : array;

    /**
     * 记忆体的默认值
     * @return array
     */
    abstract public static function __attrs() : array;

    public function getName(): string
    {
        return static::recallName();
    }

    public static function recallName(): string
    {
        return ContextUtils::normalizeMemoryName(static::class);
    }


    /**
     * @param Cloner $cloner
     * @param string|null $id
     * @return static
     */
    public static function find(Cloner $cloner, string $id = null) : Recall
    {
        $def = static::getMemoryDef($cloner->mind);
        return new static(
            $id ?? $def->makeScopeId($cloner),
            $def,
            $cloner
        );
    }

    protected static function getMemoryDef(Mindset $mind) : MemoryDef
    {
        $name = static::recallName();
        $memoryReg = $mind->memoryReg();

        if (!$memoryReg->hasDef($name)) {

            $memoryMeta = new MemoryMeta([
                'name' => $name,
                'title' => static::class,
                'desc' => '',
                'scopes' => static::__scopes(),
                'attrs' => static::__attrs(),
            ]);

            $memoryReg->registerDef($memoryMeta->toWrapper());
        }

        return $memoryReg->getDef($name);
    }

    public function toInstanceStub(): ClonerInstanceStub
    {
        return new RecallStub($this->_id, static::class);
    }

    public static function selfRegisterToMind(Mindset $mindset, bool $force = false): void
    {
        $mindset->memoryReg()->registerDef(static::getMemoryDef($mindset), !$force);
    }


    private function __clone()
    {
    }

}