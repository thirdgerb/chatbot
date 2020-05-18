<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\ClonePipes;

use Closure;
use Commune\Blueprint\Framework\Request\AppRequest;
use Commune\Blueprint\Framework\Request\AppResponse;
use Commune\Blueprint\Ghost\Cloner\ClonerLogger;
use Commune\Framework\Pipes\ARequestPipe;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CloneInputPipe extends ARequestPipe
{

    public function __construct(ClonerLogger $logger)
    {
        parent::__construct($logger);
    }

    protected function doHandle(AppRequest $request, Closure $next): AppResponse
    {
        return $next($request);
    }
}