<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\MindReg;

use Commune\Blueprint\Ghost\MindDef\Def;
use Commune\Blueprint\Ghost\Exceptions\DefNotDefinedException;
use Commune\Support\Registry\Category;

/**
 * 存放各种多轮对话逻辑单元的仓库.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface DefRegistry
{

    /**
     * 重置所有的逻辑. 非常危险, 需要做好备份才行.
     */
    public function reset() : void;

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

    /*------- 搜索 --------*/

    /**
     * 遍历.
     * @return \Generator | Def[]
     */
    public function each() : \Generator;

    /**
     * 用通配符查找可能的 id
     * @param string $query
     * @param int $offset
     * @param int $limit
     * @return string[]
     */
    public function searchIds(
        string $query,
        int $offset = 0,
        int $limit = 20
    ) : array;

    /**
     * @param string $query
     * @param int $offset
     * @param int $limit
     * @return Def[]
     */
    public function searchDefs(
        string $query,
        int $offset = 0,
        int $limit = 20
    ) : array;

    /**
     * 用通配符计算出匹配的数量.
     * @param string $query
     * @return int
     */
    public function searchCount(string $query) : int;

    /**
     * @param int $offset
     * @param int $limit
     * @return Def[]
     */
    public function paginate(int $offset = 0, int $limit = 20) : array;


    /**
     * @param int $offset
     * @param int $limit
     * @return string[]
     */
    public function paginateIds(int $offset = 0, int $limit = 20) : array;
}