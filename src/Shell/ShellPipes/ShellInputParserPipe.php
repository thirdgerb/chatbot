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
use Commune\Blueprint\Shell\Responses\ShellResponse;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ShellInputParserPipe extends AShellInputPipe
{
    /**
     * @inheritdoc
     */
    protected function doHandle(ShellRequest $request, \Closure $next): ShellResponse
    {
        $input = $request->getInput();
        $message = $input->getMessage();

        $parser = $this->session->shell->getInputParser(
            $this->session->container,
            $message
        );

        if (!isset($parser)) {
            return $next($request);
        }

        $replace = $parser($message);
        if (isset($replace)) {
            $input->setMessage($replace);
        }

        return $next($request);
    }


}