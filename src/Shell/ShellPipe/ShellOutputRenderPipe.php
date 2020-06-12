<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\ShellPipe;

use Commune\Blueprint\Shell\Requests\ShellRequest;
use Commune\Blueprint\Shell\Requests\ShlOutputRequest;
use Commune\Blueprint\Shell\Responses\ShellResponse;
use Commune\Blueprint\Shell\Responses\ShlOutputResponse;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ShellOutputRenderPipe extends AShellOutputPipe
{

    /**
     * @param ShlOutputRequest $request
     * @param \Closure $next
     * @return ShlOutputResponse
     */
    protected function doHandle(ShellRequest $request, \Closure $next): ShellResponse
    {
        $outputs = $request->getOutputs();

        $shell = $this->session->shell;
        $container = $this->session->container;

        $rendered = [];
        foreach ($outputs as $output) {

            $message = $output->getMessage();
            $render = $shell->getOutputRenderer($container, $message);

            // 可以渲染.
            if (isset($render)) {
                $messages = $render($message, $this->session);
                $rendered = array_merge($rendered, $output->derive(...$messages));

            // 无法渲染
            } else {
                $rendered[] = $output;
            }
        }

        $request->setOutputs($rendered);
        return $next($request);
    }



}