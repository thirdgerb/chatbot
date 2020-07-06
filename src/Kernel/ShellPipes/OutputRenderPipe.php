<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\ShellPipes;

use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Kernel\Protocals\ShellInputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellInputResponse;
use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;
use Commune\Blueprint\Shell;
use Commune\Blueprint\Shell\Render\Renderer;
use Commune\Protocals\HostMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OutputRenderPipe extends AShellPipe
{
    protected function handleInput(
        ShellInputRequest $request,
        \Closure $next
    ): ShellInputResponse
    {
        return $next($request);
    }

    protected function handleOutput(
        ShellOutputRequest $request,
        \Closure $next
    ): ShellOutputResponse
    {

        $outputs = $request->getOutputs();

        if (empty($outputs)) {
            return $next($request);
        }

        $shell = $this->session->shell;
        $container = $this->session->container;

        // 输出消息.
        $renderedOutputs = [];
        // 意图消息拉出来.
        $intents = [];

        $sessionId = $request->getSessionId();

        foreach ($outputs as $output) {

            $message = $output->getMessage();
            if ($message instanceof HostMsg\IntentMsg) {
                $intents[] = $message;
            }

            // 用协议中定义过的渲染器渲染一遍.
            $rendered = $this->eachRenderer($shell, $container, $message);

            // 最终也没有结果.
            if (!isset($rendered)) {
                $renderedOutputs[] = $output;
                continue;
            }

            // 消息渲染出来没了
            if (empty($rendered)) {
                continue;
            }

            // 有渲染结果.
            $renderedOutputs = array_reduce(
                $rendered,
                function($renderedOutputs, HostMsg $message) use ($output, $sessionId){
                    $renderedOutputs[] = $output->divide($message, $sessionId);
                    return $renderedOutputs;
                },
                $renderedOutputs
            );
        }

        $request->setOutputs($renderedOutputs);

        // 进入下一节.
        /**
         * @var ShellOutputResponse $response
         */
        $response = $next($request);
        $response->setIntents(...$intents);

        return $response;
    }

    /**
     * @param Shell $shell
     * @param ReqContainer $container
     * @param HostMsg $message
     * @return HostMsg[]|null
     */
    protected function eachRenderer(
        Shell $shell,
        ReqContainer $container,
        HostMsg $message
    ) : ? array
    {

        // 遍历寻找所有的 renderer
        $gen = $shell->eachProtocalHandler(
            $container,
            $message,
            Renderer::class
        );

        // 遍历.
        foreach ($gen as $renderer) {
            /**
             * @var Renderer $renderer
             */
            $rendered = $renderer($message);

            // 没有渲染结果, 跳过去
            if (isset($rendered)) {
                return $rendered;
            }
        }

        return null;
    }



}