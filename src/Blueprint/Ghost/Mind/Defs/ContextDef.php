<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Mind\Defs;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Ucl;

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

    /**
     * @return ContextParamsManager
     */
    public function getParamsManager() : ContextParamsManager;

    /**
     * 封装 Context 对象.
     *
     * @param Cloner $cloner
     * @param Ucl $ucl
     * @return Context
     */
    public function wrapContext(Cloner $cloner, Ucl $ucl) : Context;

    /**
     * 所有的 Context 同时也是一个 StageDef
     * @return StageDef
     */
    public function asStageDef() : StageDef;

    /*------- stage -------*/

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
     * 获取当前 Context 配置下所有的 stage 名称
     * @param bool $isFullname      是否显示全称.
     * @return string[]
     */
    public function getStageNames(bool $isFullname = false) : array;



}