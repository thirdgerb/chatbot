<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost;

use ArrayAccess;
use IteratorAggregate;
use Commune\Blueprint\Ghost\Cloner\ClonerInstance;
use Commune\Blueprint\Ghost\Mind\Defs\ContextDef;
use Commune\Blueprint\Ghost\Exceptions\NotInstanceException;
use Commune\Protocals\Host\Convo\ContextMsg;
use Commune\Support\DI\Injectable;
use Illuminate\Support\Collection;

/**
 * 当前语境. 用来读写当前语境的变量.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Context extends
        ArrayAccess, // 默认用数组方式来获取参数. 也可以用 getter setter
        IteratorAggregate,
        ClonerInstance,
        Injectable // Context 可以用各种方式依赖注入
{
    const NAMESPACE_SEPARATOR = '.';
    const STAGE_SEPARATOR = '_';

    /*----- properties -----*/

    /**
     * Context 名称
     * @return string
     */
    public function getName() : string;


    /**
     * @return string
     * @throws NotInstanceException
     */
    public function getId() : string;

    /**
     * @return int
     */
    public function getPriority() : int;

    /**
     * @return ContextDef
     */
    public function getDef() : ContextDef;


    /*----- entity -----*/

    /**
     * 按顺序第一个未被填满的 Query 名称.
     * @return null|string
     */
    public function dependEntity() : ? string /* entityName */;

    /**
     * @return Collection
     */
    public function getQuery() : Collection;

    /*----- assignment -----*/

    public function merge(array $data) : void;

    /*----- array -----*/

    /**
     * 获取上下文记忆的变量值.
     * @return array
     */
    public function toMemorableData() : array;

    /**
     * 递归地获取所有属性的值.
     * @return array
     */
    public function toArray(): array;

    /**
     * 转化为 ContextMsg
     * @return ContextMsg
     */
    public function toContextMsg() : ContextMsg;

}