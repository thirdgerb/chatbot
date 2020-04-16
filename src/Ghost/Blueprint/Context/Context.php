<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Context;

use ArrayAccess;
use Commune\Ghost\Blueprint\Memory\Recollection;
use Commune\Ghost\Blueprint\Definition\ContextDef;
use Commune\Ghost\Blueprint\Runtime\Node;
use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Ghost\Blueprint\Convo\SessionInstance;
use Commune\Support\DI\Injectable;

/**
 * 当前语境. 用来读写当前语境的变量.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Context extends ArrayAccess, ArrayAndJsonAble, SessionInstance, Injectable
{

//    /*----- 状态 -----*/
//
//    public function isPrepared() : bool;
//
//    public function isActive() : bool;
//
//    public function isSleeping() : bool;
//
//    public function isThread() : bool;

    /*----- 数据 -----*/

    /**
     * 合并 Data 到当前数据.
     * @param array $data
     */
    public function merge(array $data) : void;

    /**
     * 重置当前数据
     * @param array $data
     */
    public function reset(array $data) : void;

    /*----- 属性 -----*/

    public function getName() : string;

    public function getId() : string;

    public function getDef() : ContextDef;

    public function getRecollection() : Recollection;

    /*----- 方法 -----*/

    /**
     * 将一个 Context 生成为一个 Node 节点.
     * @return Node
     */
    public function toNewNode() : Node;
}