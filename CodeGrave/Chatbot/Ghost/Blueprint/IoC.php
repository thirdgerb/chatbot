<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Ghost\Blueprint;

use Psr\Container\ContainerInterface;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface IoC extends ContainerInterface
{
    /*---------- container ----------*/

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