<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Routes;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Routing\Matcher;
use Commune\Support\DI\Injectable;

/**
 * 多轮对话状态变更时的路径事件
 *
 * 多轮对话在 状态 A 时, 会根据上下文触发一个路由事件 (Route),
 * StageDef 处理这个路由事件的结果会导致 多轮对话进入 状态 B.
 *
 * 每个 Route 都有下一步的处理逻辑.
 *
 *  $stageDef->onRoute(Route $route) : ? Operator;
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Context $self         Route 操作者所处的语境.
 */
interface Route extends Injectable
{
    /**
     * 在当前上下文中通过抽象获取一个对象.
     *
     * @param string $abstract
     * @param array $parameters
     * @return mixed
     */
    public function make(string $abstract, array $parameters = []);

    /**
     * 用依赖注入的方式调用一个 callable.
     * 与laravel 的区别在于, $parameters 允许用 interface => $instance 的方式注入临时依赖.
     *
     * @param callable|string $caller
     * @param array $parameters
     * @return mixed
     */
    public function call($caller, array $parameters = []);

    /**
     * 获取上下文相关的依赖注入对象.
     * Stage::call , Stage::make 方法都会注入这些对象.
     * @return array
     */
    public function getContextualInjections() : array;

    /**
     * 对当前对话进行匹配.
     * @return Matcher
     */
    public function matcher() : Matcher;

    /**
     * 从开头重新走 Context 的流程.
     *
     * @param bool $reset
     * @return Operator
     */
    public function restart(bool $reset = false) : Operator;

    /**
     * 沿着一个或者多个 Stage 的路径前进.
     * 会插入到当前管道的头部.
     *
     * 例如管道: A B C ; 调用 next(E, F, G); 结果 E F G A B C
     *
     * @param string[] ...$stageNames
     * @return Operator
     */
    public function next(...$stageNames) : Operator;


    /**
     * 沿着多个 Stage 前进, 并且变更之前的 Stage
     *
     * @param string[] ...$stageNames
     * @return Operator
     */
    public function swerve(...$stageNames) : Operator;
}