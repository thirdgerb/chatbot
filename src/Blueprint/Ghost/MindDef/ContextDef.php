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
use Commune\Blueprint\Ghost\Ucl;
use Commune\Blueprint\Ghost\Context\ParamCollection;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
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

    /**
     * @param Dialog $current
     * @return array|null
     */
    public function comprehendPipes(Dialog $current) : ? array;


    /*------- properties -------*/

    /**
     * @return string[]
     */
    public function getScopes() : array;

    /**
     * query 参数的默认值
     * @return ParamCollection
     */
    public function getQueryDefaults() : ParamCollection;

    /**
     * @return ParamCollection
     */
    public function getParamsDefaults() : ParamCollection;

    /**
     * 依赖字段. 启动的时候默认会走这些字段来.
     * @return string[]
     */
    public function getDependingNames() : array;

    /**
     * 可以从 comprehension entity 直接获取的属性.
     * @return string[]
     */
    public function getEntityNames() : array;

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
     * @return null|string
     */
    public function onCancelStage() : ? string;

    /**
     * @return null|string
     */
    public function onQuitStage() : ? string;

    /**
     * @return array
     */
    public function commonStageRoutes() : array;

    /**
     * @return array
     */
    public function commonContextRoutes() : array;

    /*------- stage -------*/


    /**
     * @param string $stageName
     * @return StageDef
     */
    public function getPredefinedStage(string $stageName) : StageDef;

    /**
     * 获取当前 Context 配置下所有的 stage 名称
     * @param bool $isFullname      是否显示全称.
     * @return string[]
     */
    public function getPredefinedStageNames(bool $isFullname = false) : array;



}