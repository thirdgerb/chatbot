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
use Commune\Blueprint\Ghost\Convo\Conversation;
use Commune\Blueprint\Ghost\Convo\ConvoScope;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ContextDef extends Def
{

    /*------- properties -------*/

    /**
     * 优先级, 用于抢占当前 Thread
     * @return int
     */
    public function getPriority() : int;

    /**
     * 所有需要填满的属性, 不填满时通常会触发多轮对话来要求输入.
     * @return string[]
     */
    public function getEntityNames() : array;


    /*------- intend -------*/

    /**
     * 公共类意图可以被全局访问到.
     * 否则无法用意图的方式命中.
     * @return bool
     */
    public function isPublic() : bool;

    /*------- scope -------*/

    /**
     * Context 上下文是否是长程的.
     * 如果是短程的, 一旦不被持有, 就会立刻销毁.
     *
     * 长程的分两种, 一种是 ::getScopes() 为空, 这是 Session 级的记忆, 会随着 Session 销毁掉.
     * 另一种则是 ::getScopes() 不为空, 则不会销毁, 而是长期保存着. 除非主动消除.
     *
     * @return bool
     */
    public function isLongTerm() : bool;

    /**
     * 获取上下文记忆的作用域.
     * 可以从已有中间选一个或者多个
     *
     * @see ConvoScope
     * @return array
     */
    public function getScopes() : array ;

    /**
     * 根据当前作用域生成一个全局唯一的 ID.
     * 也可以不和 Scope 相关.
     *
     * @param ConvoScope $scope
     * @return string
     */
    public function makeId(ConvoScope $scope) : string;

    /*------- methods -------*/

    /**
     * 根据传入参数, 生成一个新的 Context 实例.
     *
     * @param array $assignment
     * @param Conversation $conversation
     * @return Context
     */
    public function newContext(array $assignment = [], Conversation $conversation) : Context;

    /*------- routing -------*/

    /**
     * Context 语境下公共的 contextRoutes
     * 理论上每一个 Stage 都默认继承, 也可以选择不继承.
     *
     * 在 wait 状态下, 可以跳转直达的 Context 名称.
     * 允许用 * 作为通配符.
     *
     * @param Conversation $conversation
     * @return string[]
     */
    public function contextRoutes(Conversation $conversation) : array;

    /**
     * Context 语境下公共的 stageRoutes
     * 理论上每一个 Stage 都默认继承, 也可以选择不继承.
     *
     * 在 wait 状态下, 可以跳转直达的 Context 内部 Stage 的名称.
     * 允许用 * 作为通配符.
     *
     * @param Conversation $conversation
     * @return string[]
     */
    public function stageRoutes(Conversation $conversation) : array;

    /**
     * Context 语境下公共的 Pipes 管道.
     * 理论上每一个 Stage 都默认继承, 也可以选择不继承.
     *
     * @param Conversation $conversation
     * @return string[]
     */
    public function comprehendPipes(Conversation $conversation) : array;


    /*------- stage -------*/

    /**
     * 获取 Context 的初始 Stage. 所有 Context 至少有这一个 Stage.
     * @return StageDef
     */
    public function getInitialStageDef() : StageDef;

    public function hasStage(string $stageName) : bool;

    public function getStage(string $stageName) : StageDef;

    /**
     * 获取当前 Context 下所有的 stage 名称
     * @param bool $isFullname      是否显示全称.
     * @return string[]
     */
    public function getStageNames(bool $isFullname = false) : array;



}