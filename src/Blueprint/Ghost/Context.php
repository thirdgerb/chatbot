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
use Commune\Blueprint\Ghost\Context\Dependable;
use Commune\Blueprint\Ghost\Memory\Recollection;
use Commune\Blueprint\Ghost\Runtime\Task;
use IteratorAggregate;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Protocals\HostMsg\Convo\ContextMsg;
use Commune\Support\DI\Injectable;

/**
 * 当前语境. 用来读写当前语境的变量.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Context extends
        ArrayAccess, // 默认用数组方式来获取参数. 也可以用 getter setter
        IteratorAggregate,
        Dependable,
        Injectable // Context 可以用各种方式依赖注入
{
    const NAMESPACE_SEPARATOR = '.';
    const STAGE_SEPARATOR = '_';
    const CONTEXT_STAGE_DELIMITER = '.';

    /*----- status -----*/

    // 上下文中新建的语境.
    const CREATED   = 0;
    const ALIVE     = 1 << 1;
    const WAITING   = 1 << 10;


    const AWAIT     = 1 << 11 | self::WAITING;
    const DEPENDING = 1 << 12 | self::WAITING;
    const BLOCKING  = 1 << 13 | self::WAITING;
    const CALLBACK  = 1 << 14 | self::WAITING;
    const SLEEPING  = 1 << 15 | self::WAITING;
    const YIELDING  = 1 << 16 | self::WAITING;
    const DYING     = 1 << 17 | self::WAITING;

    /*----- properties -----*/

    const CREATE_FUNC = 'create';

    /**
     * @param Cloner $cloner
     * @param Ucl $ucl
     * @return static
     */
    public static function create(Cloner $cloner, Ucl $ucl) : Context;

    /**
     * Context 名称
     * @return string
     */
    public function getName() : string;

    /**
     * @return string
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

    /**
     * @return Cloner
     */
    public function getCloner() : Cloner;

    /**
     * @return Task
     */
    public function getTask() : Task;

    /**
     * @return Ucl
     */
    public function getUcl() : Ucl;

    /**
     * @param string $stage
     * @return Ucl
     */
    public function getStage(string $stage = '') : Ucl;

    /**
     * @param string[] $stages
     * @return Ucl[]
     */
    public function getStages(array $stages) : array;

    /*----- entity -----*/

    /**
     * 按顺序第一个未被填满的 Query 名称.
     * @return null|string
     */
    public function depending() : ? string /* entityName */;

    /**
     * @return bool
     */
    public function isFulfilled() : bool;

    /**
     * @return bool
     */
    public function isChanged() : bool;

    /*----- query -----*/

    /**
     * @return array
     */
    public function getQuery() : array;

    /*----- assignment -----*/

    /**
     * 没有 query 的属性部分.
     *
     * @return Recollection
     */
    public function getRecollection() : Recollection;

    /**
     * @param array $data
     */
    public function merge(array $data) : void;

    /**
     * 主动保存当前数据.
     */
    public function save() : void;

    /*----- array -----*/

    /**
     * 获取上下文记忆的变量值.
     * @return array
     */
    public function toData() : array;

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