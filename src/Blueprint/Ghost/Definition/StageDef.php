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

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Routes\Activate;
use Commune\Blueprint\Ghost\Routes\Intend;
use Commune\Blueprint\Ghost\Routes\React;
use Commune\Blueprint\Ghost\Routes\Retrace;
use Commune\Blueprint\Ghost\Routes\Route;
use Commune\Blueprint\Ghost\Stage;
use Commune\Blueprint\Ghost\Operator\Operator;

/**
 * Stage 的封装对象
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface StageDef
{

    /*------- properties -------*/

    /**
     * Stage 在 Context 内部的唯一ID
     * @return string
     */
    public function getName() : string;

    /**
     * stage 的全名, 通常对应 IntentName
     * @return string
     */
    public function getFullname() : string;

    /**
     * 所属 Context 的名称.
     * @return string
     */
    public function getContextName() : string;

    /*------- relations -------*/

    /**
     * @param Cloner $cloner
     * @return ContextDef
     */
    public function findContextDef(Cloner $cloner) : ContextDef;

    /**
     * @return IntentDef
     */
    public function asIntentDef() : IntentDef;

    /*------- routes -------*/


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
     * 当前 Stage 自定义的理解管道.
     * @param Cloner $cloner
     * @return string[]
     */
    public function comprehendPipes(Cloner $cloner) : array;

    /*------- intend to stage -------*/

    /**
     * @param Cloner $cloner
     * @param Intend $route
     * @return Operator|null
     */
    public function onIntend(
        Cloner $cloner,
        Intend $route
    ) : ? Operator;

    /**
     * @param Cloner $cloner
     * @param Activate $route
     * @return Operator
     */
    public function onActivate(
        Cloner $cloner,
        Activate $route
    ) : Operator;

    /**
     * @param Cloner $cloner
     * @param React $route
     * @return Operator
     */
    public function onReact(
        Cloner $cloner,
        React $route
    ) : Operator;

    /**
     * @param Cloner $cloner
     * @param Retrace $retrace
     * @return Operator
     */
    public function onRetrace(
        Cloner $cloner,
        Retrace $retrace
    ) : Operator;
}