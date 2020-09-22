<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\MindDef;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Ucl;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
interface ContextDef extends Def
{
    const START_STAGE_NAME = '';

    /*------- definition -------*/

    /**
     * 优先级, 用于抢占当前 Thread
     * @return int
     */
    public function getPriority() : int;

    /*------- properties -------*/

    /**
     * 语境的作用域, 维度 @see Cloner\ClonerScope
     * 定义的参数会自动添加到 query 上.
     * 同样也会影响到 Context 的记忆维度.
     *
     * @return string[]
     */
    public function getScopes() : array;

    /**
     * query 参数的默认值
     * @return string[]
     */
    public function getQueryNames() : array;

    /**
     * 依赖字段. 启动的时候默认会走这些字段来.
     * @return string[]
     */
    public function getDependingNames() : array;

    /*------- redirect -------*/

    /**
     * 当前 Context 因为意图而被触发时.
     *
     * @param Dialog $prev
     * @param Ucl $current
     * @return Operator|null
     */
    public function onRedirect(Dialog $prev, Ucl $current) : ? Operator;


    /*------- relation -------*/

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

    /**
     * @return MemoryDef
     */
    public function asMemoryDef() : MemoryDef;

    /*------- context 内的通用配置. -------*/

    /**
     * @return null|string
     */
    public function firstStage() : ? string;

    /**
     * 语境相关的默认多轮对话策略.
     *
     * @return ContextStrategyOption
     */
    public function getStrategy() : ContextStrategyOption;

    /*------- stage -------*/


    /**
     * @return StageDef[]|\Generator
     */
    public function eachPredefinedStage() : \Generator;

    /**
     * 获取当前 Context 配置下所有的 stage 名称
     * @param bool $isFullname      是否显示全称.
     * @return string[]
     */
    public function getPredefinedStageNames(bool $isFullname = false) : array;

    /**
     * @param string $name
     * @return StageDef|null
     */
    public function getPredefinedStage(string $name) : ? StageDef;


}