<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint;

use Commune\Ghost\Blueprint\Routing\Route;
use Psr\Container\ContainerInterface;

/**
 * 多轮对话调度器. Dialog Manager
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 以下的子属性, 都可以在 call 的时候进行依赖注入.
 *
 * @property-read GhostConfig $config shell的配置.
 * @property-read Memory $memory 记忆单元.
 * @property-read Mind $mind 思维单元
 * @property-read Session $session 会话信息
 * @property-read Context $context 当前的语境
 * @property-read Runtime $runtime 当前的运行状态.
 * @property-read Stage $stage 当前的 Stage 状态机.
 * @property-read Route $route 经历过的路径
 */
interface Ghost extends ContainerInterface
{
    /**
     * laravel 风格生成实例
     * @param string $abstract
     * @param array $parameters
     * @return mixed
     */
    public function make(string $abstract, array $parameters = []) ;

    /**
     * 调用一个 callable, 并对它进行依赖注入
     * 可以注入 Dialog 相关的一些上下文对象.
     *
     * @param callable $caller
     * @param array $parameters
     * @param string $method
     * @return mixed
     */
    public function call(callable $caller, array $parameters = [], string $method = '');

}