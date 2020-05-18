<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Handlers;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Message\Host\Convo\IText;
use Commune\Blueprint\Ghost\Request\GhostRequest;
use Commune\Blueprint\Ghost\Request\GhostResponse;
use Commune\Blueprint\Framework\Pipes\RequestPipe;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class GhostRequestHandler
{
    /**
     * @var string[]
     */
    protected $middleware;

    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * RequestHandler constructor.
     * @param Cloner $cloner
     */
    public function __construct(Cloner $cloner)
    {
        $this->cloner = $cloner;
        $this->middleware = $cloner->config->clonePipes;
    }


    public function __invoke(GhostRequest $request) : GhostResponse
    {

        $end = function(GhostRequest $request) : GhostResponse {
            return $request->output(new IText('hello world'));
//            return $request->fail(AppResponse::NO_CONTENT);
        };

        if (empty($this->middleware)) {
            return $end($request);
        }

        $pipeline = $this->cloner->buildPipeline(
            $this->middleware,
            RequestPipe::HANDLER_FUNC,
            $end
        );

        return $pipeline($request);
    }

}