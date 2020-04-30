<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Definition;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Snapshot\Task;
use Illuminate\Support\Collection;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ContextDef extends Def
{

    /*------- definition -------*/

    /**
     * 优先级, 用于抢占当前 Thread
     * @return int
     */
    public function getPriority() : int;

    /**
     * 公共语境, 可以作为意图被全局访问到.
     * Query 中不足的元素将用多轮对话向用户询问.
     *
     * @return bool
     */
    public function isPublic() : bool;


    /**
     * 定义上下文记忆的作用域.
     * 会把作用域中的元素自动加入到 Query 中间.
     *
     * @see ConvoScope
     * @return array
     */
    public function getScopes() : array ;


    /*------- parameters -------*/

    /**
     * @param string $name
     * @return bool
     */
    public function hasParameter(string $name) : bool;

    /**
     * @param string $name
     * @return ContextParameter
     */
    public function getParameter(string $name) : ContextParameter;

    /**
     * @return ContextParameter[]
     */
    public function getParameters() : array;

    /**
     * @return Collection of ContextParameter[]
     */
    public function getQueryParams() : Collection;

    /**
     * @return Collection of ContextParameter[]
     */
    public function getLongTermParams() : Collection;

    /**
     * @return Collection of ContextParameter[]
     */
    public function getShortTermParams() : Collection;

    /**
     * 过滤 Entity 的值. Entity 默认的每一项都是数组.
     * @param array $entities
     * @return array
     */
    public function parseIntentEntities(array $entities) : array;

    /**
     * 所有需要填满的属性, 不填满时, 要么拒绝对话, 要么启动一个多轮对话去检查.
     * @return string[]
     */
    public function getQueryNames() : array;

    /**
     * Context 的默认值.
     * @return array
     */
    public function getDefaultValues() : array;

    /**
     * 根据当前作用域生成一个全局唯一的 ID.
     *
     * @param Cloner $cloner
     * @param array $queries
     * @return string
     */
    public function makeId(Cloner $cloner, array $queries) : string;

    /*------- methods -------*/

    /**
     * 将 Context 封装成对象.
     *
     * @param Cloner $cloner
     * @param Task $frame
     * @return Context
     */
    public function wrapContext(Cloner $cloner, Task $frame) : Context;

    /*------- routing -------*/

    /**
     * Context 语境下公共的 contextRoutes
     * 理论上每一个 Stage 都默认继承, 也可以选择不继承.
     *
     * 在 wait 状态下, 可以跳转直达的 Context 名称.
     * 允许用 * 作为通配符.
     *
     * @param Cloner $cloner
     * @return string[]
     */
    public function contextRoutes(Cloner $cloner) : array;

    /**
     * Context 语境下公共的 stageRoutes
     * 理论上每一个 Stage 都默认继承, 也可以选择不继承.
     *
     * 在 wait 状态下, 可以跳转直达的 Context 内部 Stage 的名称.
     * 允许用 * 作为通配符.
     *
     * @param Cloner $cloner
     * @return string[]
     */
    public function stageRoutes(Cloner $cloner) : array;

    /**
     * Context 语境下公共的 Pipes 管道.
     * 理论上每一个 Stage 都默认继承, 也可以选择不继承.
     *
     * @param Cloner $cloner
     * @return string[]
     */
    public function comprehendPipes(Cloner $cloner) : array;


    /*------- stage -------*/

    /**
     * 获取 Context 的初始 Stage. 所有 Context 至少有这一个 Stage.
     * @return StageDef
     */
    public function getInitialStageDef() : StageDef;

    /**
     * @param string $stageName
     * @return bool
     */
    public function hasStage(string $stageName) : bool;

    /**
     * @param string $stageName
     * @return StageDef
     */
    public function getStage(string $stageName) : StageDef;

    /**
     * 获取当前 Context 下所有的 stage 名称
     * @param bool $isFullname      是否显示全称.
     * @return string[]
     */
    public function getStageNames(bool $isFullname = false) : array;



}