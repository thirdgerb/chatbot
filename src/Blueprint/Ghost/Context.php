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
use Commune\Blueprint\Ghost\Memory\Memorable;
use Commune\Blueprint\Ghost\Runtime\Node;
use Commune\Blueprint\Ghost\Exceptions\NotInstanceException;
use Commune\Blueprint\Ghost\Snapshot\Task;
use Commune\Support\DI\Injectable;

/**
 * 当前语境. 用来读写当前语境的变量.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Context extends
        ArrayAccess, // 默认用数组方式来获取参数. 也可以用 getter setter
        Memorable,
        Injectable // Context 可以用各种方式依赖注入
{
    const NAMESPACE_SEPARATOR = '.';

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


    /*----- entity -----*/

    /**
     * 按顺序第一个未被填满的 Query 名称.
     * @return null|string
     */
    public function dependQuery() : ? string /* entityName */;

    /*----- array -----*/

    /**
     * 获取所有的变量值.
     * @return array
     */
    public function toData() : array;

    /**
     * @return array
     */
    public function toQuery() : array;

    /**
     * 递归地获取所有属性的值.
     * @return array
     */
    public function toArray(): array;

    /*----- node -----*/

    /**
     * 将一个 Context 生成为一个新的 Node 节点.
     * @return Node
     */
    public function toNewNode() : Node;

    /**
     * @return Task
     */
    public function toTask() : Task;
}