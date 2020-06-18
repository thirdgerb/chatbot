<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\Handlers;

use Commune\Blueprint\Framework\Pipes\RequestPipe;
use Commune\Blueprint\Kernel\Handlers\ShellOutputHandler;
use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;
use Commune\Blueprint\Shell\ShellSession;
use Commune\Contracts\Messenger\MessageDB;
use Commune\Framework\Spy\SpyAgency;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShellOutputHandler implements ShellOutputHandler
{

    protected $middleware = [

    ];

    /**
     * @var ShellSession
     */
    protected $session;

    public function __construct(ShellSession $cloner, array $middleware = null)
    {
        $this->session = $cloner;
        $this->middleware = $middleware ?? $this->middleware;
        SpyAgency::incr(static::class);
    }



    public function __invoke(ShellOutputRequest $request): ShellOutputResponse
    {
        $response = $request->validate();

        if (isset($response)) {
            return $response;
        }

        if ($request->isAsync()) {
            $request = $this->fillOutputsInRequest($request);
        }

        if (empty($middleware)) {
            return $this->finale($request);
        }

        // 生成管道.
        $pipeline = $this->session->buildPipeline(
            $middleware,
            RequestPipe::HANDLER_FUNC,
            function(ShellOutputRequest $request) : ShellOutputResponse{
                return $this->finale($request);
            }
        );

        // 通过管道运行.
        return $pipeline($request);
    }

    protected function finale(ShellOutputRequest $request) : ShellOutputResponse
    {
        return $request->response();
    }


    /**
     * 从数据存储中读取批次的所有消息, 会包含 input message.
     *
     * @param ShellOutputRequest $request
     * @return ShellOutputRequest
     */
    protected function fillOutputsInRequest(ShellOutputRequest $request) : ShellOutputRequest
    {
        /**
         * @var MessageDB $db
         */
        $db = $this->session->container->get(MessageDB::class);

        $outputs = $db->where()
            ->batchIdIs($request->getTraceId())
            ->get();

        $request->setOutputs($outputs);

        // 过滤到输入信息.
        return $request;
    }

    public function __destruct()
    {
        SpyAgency::decr(static::class);
    }
}