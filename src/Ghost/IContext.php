<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Definition\ContextDef;
use Commune\Blueprint\Ghost\Memory\Recollection;
use Commune\Blueprint\Ghost\Memory\Stub;
use Commune\Blueprint\Ghost\Runtime\Node;
use Commune\Ghost\Context\ContextStub;
use Commune\Ghost\Memory\TMemorable;
use Commune\Ghost\Prototype\Runtime\INode;
use Commune\Support\DI\TInjectable;


/**
 * 上下文语境的容器.
 * 持有 Context 的上下文记忆 Recollection, 用于读/写真正的数据.
 *
 *
 * 通常不是 New 出来, 而是 Cloner::findContext() 或者 Cloner::newContext() 来获取
 * @see Cloner
 *
 * 最终通过 ContextDef::wrapContext() 完成包装.
 * @see ContextDef
 *
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IContext implements Context
{
    use TInjectable, TMemorable;

    /**
     * @var ContextDef
     */
    protected $_def;

    public function __construct(
        ContextDef $def,
        Cloner $cloner,
        Recollection $recollection
    )
    {
        $this->_def = $def;
        $this->_cloner = $cloner;
        $this->_recollection = $recollection;
    }

    /*----- property -----*/

    public function getName(): string
    {
        return $this->_def->getName();
    }

    public function getPriority(): int
    {
        return $this->_def->getPriority();
    }

    /*----- entity -----*/

    public function toEntities(): array
    {
        $entities = $this->_def->getEntityNames();
        $data = [];
        foreach ($entities as $name) {
            $data[$name] = $this->offsetGet($name);
        }
        return $entities;
    }

    public function dependEntity(): ? string /* entityName */
    {
        $entities = $this->_def->getEntityNames();
        foreach ($entities as $name) {
            $value = $this->offsetGet($name);
            if (is_null($value)) {
                return $name;
            }
        }
        return null;
    }


    /*----- node -----*/

    public function toNewNode(): Node
    {
        return new INode(
            $this->getName(),
            $this->getId(),
            $this->getPriority()
        );
    }


    /*----- memorable -----*/

    public function toStub(): Stub
    {
        return new ContextStub([
            'contextId' => $this->getId(),
            'contextName' =>  $this->getName(),
        ]);
    }



    /*----- injectable -----*/

    public function getInterfaces(): array
    {
        return static::getInterfacesOf(Context::class);
    }


    public function __destruct()
    {
        $this->_def = null;
        $this->_cloner = null;
        $this->_recollection = null;
    }
}