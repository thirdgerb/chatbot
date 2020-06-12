<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Handlers;

use Commune\Blueprint\Framework\Request\AppResponse;
use Commune\Blueprint\Shell\Handlers\ShlInputReqHandler;
use Commune\Blueprint\Shell\Requests\ShlInputRequest;
use Commune\Blueprint\Shell\Responses\ShlInputResponse;
use Commune\Blueprint\Shell\ShellSession;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class InputRequestHandler implements ShlInputReqHandler
{

    /**
     * @var ShellSession
     */
    protected $session;

    public function __invoke(ShlInputRequest $request) : ShlInputResponse
    {
        $start = microtime(true);

        // 不接受异常请求.
        if (!$request->isValid()) {
            return $request->response(AppResponse::BAD_REQUEST);
        }

        // 无状态标记
        if ($request->isStateless()) {
            $this->session->noState();
        }

        $end = function(GhostRequest $request) : GhostResponse {
            return $request->fail(AppResponse::NO_CONTENT);
        };

        if (empty($this->middleware)) {
            $response = $end($request);

        } else {
            $pipeline = $this->cloner->buildPipeline(
                $this->middleware,
                RequestPipe::HANDLER_FUNC,
                $end
            );

            $response = $pipeline($request);
        }

        $end = microtime(true);
        $gap = round(($end - $start) * 1000000);
        $peak = memory_get_peak_usage();
        $this->cloner->logger->info("finish request in $gap, memory peak $peak");
        return $response;
    }
}