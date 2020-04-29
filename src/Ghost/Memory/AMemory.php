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
use Commune\Blueprint\Ghost\Cloner\ClonerScope;
use Commune\Blueprint\Ghost\Memory\Memory;
use Commune\Blueprint\Ghost\Memory\Recollection;
use Commune\Blueprint\Ghost\Memory\Stub;

/**
 * 系统的记忆模块. 允许用类的方式, 定义自己所需要的记忆体.
 *
 * 例如用类名定义:
 * class MemoFoo extends AMemory {}
 *
 * 然后调用:
 * $foo = MemoFoo::find($cloner);
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AMemory implements Memory
{
    use TMemorable;

    protected function __construct(
        Cloner $cloner,
        Recollection $recollection
    )
    {
        $this->_cloner = $cloner;
        $this->_recollection = $recollection;
    }



    /*--- abstract ---*/

    /**
     * 指定记忆的默认作用域.
     *
     * @see ClonerScope
     * @return string[]
     */
    abstract public static function getScopes() : array;

    /**
     * 定义记忆体的默认值.
     * @return array
     */
    abstract public static function stub(): array;

    /**
     * 定义记忆体的名称.
     * @return string
     */
    abstract public static function getMemoryName(): string;



    /*--- find ---*/

    public static function find(Cloner $cloner): Memory
    {
        $id = static::makeId($cloner);
        $name = static::getMemoryName();
        $scopes = static::getScopes();

        $recollection = $cloner->runtime->findRecollection($id)
            ?? $cloner->runtime->createRecollection(
                $id,
                $name,
                !empty($scopes),
                static::stub()
            );

        return new static($cloner, $recollection);
    }

    /*--- stub ---*/

    public static function makeId(Cloner $cloner): string
    {
        return $cloner->scope->makeScopeId(
            $name = static::getMemoryName(),
            $scopes = static::getScopes()
        );
    }


    public function toStub(): Stub
    {
        return new MemStub(['className' => static::class]);
    }


}