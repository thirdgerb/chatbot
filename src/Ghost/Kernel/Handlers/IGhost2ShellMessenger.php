<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Kernel\Handlers;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Contracts\Messenger\MessageDB;
use Commune\Contracts\Messenger\Messenger;
use Commune\Blueprint\Kernel\Handlers\Ghost2ShellMessenger;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Contracts\Messenger\Broadcaster;
use Commune\Ghost\IGhost;
use Commune\Ghost\Kernel\Protocals\IGhostResponse;
use Commune\Ghost\Support\ValidateUtils;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Protocals\Intercom\OutputMsg;
use Commune\Support\Utils\TypeUtils;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IGhost2ShellMessenger implements Ghost2ShellMessenger
{

    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * @param GhostResponse $protocal
     * @return GhostResponse
     */
    public function __invoke($protocal)
    {
        ValidateUtils::isArgInstanceOf($protocal, GhostResponse::class, true);

        // 设置 convoId
        $convoId = $this->cloner->getConversationId();
        $protocal->setConvoId($convoId);

        // 保存消息.
        $this->recordBatchMessages($protocal);
        // 发送异步输入消息.
        $this->sendAsyncInput($protocal);


        $outputMap = [];
        $outputs = $protocal->getOutputs();

        // 区分不同发送对象的消息.
        foreach ($outputs as $output) {
            $key = $output->getShellName() . ':' . $output->getSessionId();
            $outputMap[$key] = $output;
        }

        // 如果请求不成功, 或者请求做了无状态处理, 都直接返回结果.
        if (!$protocal->isSuccess() || $this->cloner->isStateless()) {
            return $this->directResponse($protocal);
        }

        // 会先广播, 再返回响应.
        return $this->broadcastResponse($protocal);

    }


    protected function directResponse(GhostResponse $cloneResponse) : GhostResponse
    {

        $input = $cloneResponse->getInput();

        $routes = $this->cloner->storage->shellSessionRoutes;
        $shellSessionId = $routes[$shellName] ?? $input->getSessionId();

        $outputs = $cloneResponse->getOutputs();

        $outputs = array_filter($outputs, function(OutputMsg $message) use ($shellName) {
            return $message->getShellName() === $shellName;
        });

        if ($cloneResponse->isAsync()) {
            $broadcast = $this->cloner->container->get(Broadcaster::class);
            $response = new IGhostResponse([
                'traceId' => $cloneResponse->getTraceId(),
                'shellName' => $shellName,
                'shellId' => $shellSessionId,
                'batchId' => $input->getBatchId(),
                'count' => count($outputs),
                'tiny' => true,
            ]);
            $this->broadcastGhostResponse($broadcast, $response);
            return $response;
        }

        $tiny = $cloneResponse->requireTinyResponse();
    }

    protected function broadcastResponse(GhostResponse $cloneResponse) : GhostResponse
    {

    }

    protected function broadcastGhostResponse(Broadcaster $broadcast, GhostResponse $response) : void
    {
        if (!$response->isTinyResponse()) {
            $this->cloner->logger->error(__METHOD__ . ' should not accept tiny ghost response');
            return;
        }
        $broadcast->publish($response);
    }

    protected function sendAsyncInput(GhostResponse $response) : void
    {
        $asyncInputs = $response->getAsyncInputs();
        if (!empty($asyncInputs)) {
            /**
             * @var Messenger $messenger
             */
            $messenger = $this->cloner->container->get(Messenger::class);
            $messenger->asyncSendGhostInputs(...$asyncInputs);
        }
    }

    protected function recordBatchMessages(GhostResponse $response) : void
    {
        $input = $response->getInput();
        $outputs = $response->getOutputs();

        /**
         * @var MessageDB $messageDB
         */
        $messageDB = $this->cloner->container->get(MessageDB::class);
        $messageDB->recordBatch($input, $outputs);
    }
}