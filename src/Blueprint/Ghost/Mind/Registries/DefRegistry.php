<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Mind\Registries;

use Commune\Blueprint\Ghost\Mind\Definitions\Def;
use Commune\Blueprint\Ghost\Exceptions\DefNotDefinedException;
use Commune\Support\Registry\Category;

/**
 * 存放各种多轮对话逻辑单元的仓库.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface DefRegistry
{
    /**
     * 重置注册表的 Def 缓存.
     */
    public function flushCache() : void;

    /**
     * 注册表对应的配置 ID
     * @return string
     */
    public function getMetaId() : string;

    /**
     * 配置文件的注册表.
     * @return Category
     */
    public function getMetaRegistry() : Category;

    /**
     * 配置是否存在.
     * @param string $defName
     * @return bool
     */
    public function hasDef(string $defName) : bool;

    /**
     * @param string $defName
     * @return Def
     * @throws DefNotDefinedException
     */
    public function getDef(string $defName) : Def;

    /**
     * @param Def $def
     * @param bool $notExists
     * @return bool
     */
    public function registerDef(Def $def, bool $notExists = true) : bool;


    /**
     * @return string[]
     */
    public function getAllDefIds() : array;

    /**
     * 用通配符查找可能的 id
     * @param string $wildcardId
     * @return string[]
     */
    public function searchIds(string $wildcardId) : array;

    /**
     * 用通配符计算出匹配的数量.
     * @param string $wildcardId
     * @return int
     */
    public function searchIdExists(string $wildcardId) : int;

    /**
     * 遍历.
     * @return \Generator
     */
    public function each() : \Generator;

    /**
     * @param int $offset
     * @param int $limit
     * @return Def[]
     */
    public function paginate(int $offset = 0, int $limit = 20) : array;
}