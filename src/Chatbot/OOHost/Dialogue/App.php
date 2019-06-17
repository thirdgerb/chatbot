<?php


namespace Commune\Chatbot\OOHost\Dialogue;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Psr\Container\ContainerInterface;

/**
 * dialog 中用的IOC 容器.
 * 可以用数组的方式访问依赖的对象.
 */
interface App extends ContainerInterface, \ArrayAccess
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
     * laravel 风格调用一个callable
     * @param callable $caller
     * @param array $parameters
     * @param string $method
     * @return mixed
     */
    public function call(callable $caller, array $parameters = [], string $method = '');

    /**
     * 如果实例存在, 则获取. 不用写 if has get 了.
     * @param string $id
     * @return mixed|null
     */
    public function getIf(string $id);


    /*---------- context call ----------*/

    /**
     * 调用一个上下文相关的 $interceptor
     * 会传入 context, dialog, message 作为参数.
     * 默认的返回值是 null|navigator
     *
     *
     * @param Context $self
     * @param callable $interceptor
     * @param Message|null $message
     * @return Navigator|null
     */
    public function callContextInterceptor(
        Context $self,
        callable $interceptor,
        Message $message = null
    ) : ? Navigator;

    /**
     * 调用一个上下文相关的 $prediction
     * 会传入 context, dialog, message 作为参数.
     * 默认的返回值是 bool 值
     *
     * @param Context $self
     * @param callable $prediction
     * @param Message|null $message
     * @return bool
     */
    public function callContextPrediction(
        Context $self,
        callable $prediction,
        Message $message = null
    ) : bool;
}