<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\ShellPipes;

use Commune\Blueprint\Shell\Requests\ShellRequest;
use Commune\Blueprint\Shell\Requests\ShlOutputRequest;
use Commune\Blueprint\Shell\Responses\ShellResponse;
use Commune\Blueprint\Shell\Responses\ShlOutputResponse;
use Commune\Support\Protocal\ProtocalMatcher;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ShellOutputRenderPipe extends AShellOutputPipe
{
    /**
     * @var ProtocalMatcher
     */
    private static $matcher;

    /**
     * @param ShlOutputRequest $request
     * @param \Closure $next
     * @return ShlOutputResponse
     */
    protected function doHandle(ShellRequest $request, \Closure $next): ShellResponse
    {
        $outputs = $request->getOutputs();

        $shell = $this->session->shell;
        $manager = $shell->getRenderManager();
        $container = $this->session->container;

        $renderedOutputs = [];
        foreach ($outputs as $output) {

            $rendered = $manager->render(
                $container,
                $output,
                $this->getProtocalMatcher()
            );

            $renderedOutputs = empty($rendered)
                ? $renderedOutputs
                : array_merge($renderedOutputs, $rendered);
        }

        $request->setOutputs($renderedOutputs);
        return $next($request);
    }

    protected function getProtocalMatcher() : ProtocalMatcher
    {
        return self::$matcher
            ?? self::$matcher = new ProtocalMatcher($this->session->config->outputRenderers);
    }



}