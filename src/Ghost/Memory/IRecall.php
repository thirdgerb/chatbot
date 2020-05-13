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
use Commune\Blueprint\Ghost\Memory\Memory;
use Commune\Blueprint\Ghost\Memory\Recall;
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
    use ArrayAbleToJson, TArrayAccessToMutator, TRecollection;

    const GETTER_PREFIX = '__get_';

    const SETTER_PREFIX = '__set_';


    private function __construct(string $id, bool $longTerm, Memory $memory, Cloner $cloner)
    {
        $this->_id = $id;
        $this->_longTerm = $longTerm;
        $this->_memory = $memory;
        $this->_cloner = $cloner;
    }


    public function getName(): string
    {
        return static::recallName();
    }


    abstract public static function recallName() : string;

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
            $name = static::recallName();
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


    private function __clone()
    {
    }

}