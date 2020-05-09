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
use Commune\Blueprint\Ghost\Memory;
use Commune\Blueprint\Ghost\Recall;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Arr\TArrayAccessToMutator;

/**
 * 多轮对话的记忆单元.
 * 根据 Scopes 决定是长程的还是短程(session 级别)的.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class IRecall implements Recall
{
    use ArrayAbleToJson, TArrayAccessToMutator;

    const GETTER_PREFIX = '__get_';

    const SETTER_PREFIX = '__set_';

    protected $_id;

    protected $_name;

    protected $_longTerm;

    protected $_memory;

    protected $_cloner;


    private function __construct(string $id, bool $longTerm, Memory $memory, Cloner $cloner)
    {
        $this->_id = $id;
        $this->_longTerm = $longTerm;
        $this->_memory = $memory;
        $this->_cloner = $cloner;
    }

    public function getId() : string
    {
        return $this->_id;
    }

    public function isLongTerm(): bool
    {
        return $this->_longTerm;
    }



    abstract public static function getName() : string;

    /**
     * @see ClonerScope
     * @return string[]
     */
    abstract public static function getScopes() : array;

    abstract public static function stub() : array;

    public static function find(Cloner $cloner, string $id = null) : Recall
    {
        $scope = $cloner->scope;
        $scopes = static::getScopes();
        $dimensions = $scope->getLongTermDimensionsDict($scopes);
        $longTerm = !empty($dimensions);


        if (!isset($id)) {
            $name = static::getName();
            $id = $scope->makeId($name, $dimensions);
        }

        $stub = static::stub();
        $memory = $cloner->runtime->findMemory($id, $longTerm, $stub);
        return new static($id, $longTerm, $memory, $cloner);
    }

    public function toInstanceStub(): ClonerInstanceStub
    {
        return new RecallStub($this->_id, static::class);
    }


    public function offsetExists($offset)
    {
        return $this->_memory->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        $value = $this->_memory->offsetGet($offset);
        if ($value instanceof Cloner\ClonerInstanceStub) {
            $value = $value->toInstance($this->_cloner);
        }
        return $value;
    }

    public function offsetSet($offset, $value)
    {
        if ($value instanceof Cloner\ClonerInstance) {
            $value = $value->toInstanceStub();
        }
        $this->_memory->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->_memory->offsetUnset($offset);
    }

    public function toArray(): array
    {
        return $this->_memory->toArray();
    }

    private function __clone()
    {
    }

    public function __destruct()
    {
        $this->_cloner = null;
        $this->_memory = null;
    }
}