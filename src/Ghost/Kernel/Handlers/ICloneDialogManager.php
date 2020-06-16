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

use Commune\Blueprint\CommuneEnv;
use Commune\Blueprint\Exceptions\CommuneRuntimeException;
use Commune\Blueprint\Exceptions\Runtime\BrokenRequestException;
use Commune\Blueprint\Exceptions\Runtime\BrokenConversationException;
use Commune\Blueprint\Framework\Pipes\RequestPipe;
use Commune\Blueprint\Framework\Request\AppResponse;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Kernel\Handlers\CloneDialogManager;
use Commune\Blueprint\Kernel\Protocals\CloneRequest;
use Commune\Blueprint\Kernel\Protocals\CloneResponse;
use Commune\Contracts\Log\ExceptionReporter;
use Commune\Ghost\ClonePipes\CloneApiHandlePipe;
use Commune\Ghost\ClonePipes\CloneSuperCmdPipe;
use Commune\Ghost\ClonePipes\CloneUserCmdPipe;
use Commune\Ghost\IOperate\OStart;
use Commune\Ghost\Support\ValidateUtils;
use Commune\Message\Host\SystemInt\RequestFailInt;
use Commune\Message\Host\SystemInt\SessionFailInt;
use Commune\Protocals\HostMsg\Convo\UnsupportedMsg;


/**
 * 多轮对话管理内核.
 *
 * Notice: 无法使用 CSP 模型, 因为要使用相同的 Clone 实例. 因此每个环节都是状态相关的.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ICloneDialogManager implements CloneDialogManager
{

    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * 中间件, 可以自定义.
     * @var string[]
     */
    protected $middleware = [
        CloneApiHandlePipe::class,
        CloneUserCmdPipe::class,
        CloneSuperCmdPipe::class,
    ];

    /**
     * ICloneDialogManager constructor.
     * @param Cloner $cloner
     * @param array|null $middleware
     */
    public function __construct(Cloner $cloner, array $middleware = null)
    {
        $this->cloner = $cloner;
        $this->middleware = $middleware ?? $this->middleware;
    }


    /**
     * @param CloneRequest $request
     * @return CloneResponse
     */
    public function __invoke($request)
    {
        ValidateUtils::isArgInstanceOf($request, CloneRequest::class, true);

        // 尝试记录日志.
        $message = $request->getInput()->getMessage();
        if (CommuneEnv::isDebug()) {
            $text = $message->getText();
            $this->cloner->logger->debug("receive message : \"$text\"");
        }

        // 不支持的消息.
        if ($message instanceof UnsupportedMsg) {
            $this->cloner->noState();
            return $request->fail(AppResponse::NO_CONTENT);
        }

        try {

            $middleware = $this->middleware;

            if (empty($middleware)) {
                return $this->runDialogManager($request);
            }

            $pipeline = $this->cloner->buildPipeline(
                $middleware,
                RequestPipe::HANDLER_FUNC,
                function(CloneRequest $request) : CloneResponse{
                    return $this->runDialogManager($request);
                }
            );

            // 通过管道运行.
            return $pipeline($request);

        // 会话级异常.
        } catch (BrokenConversationException $e) {
            $this->report($e);
            return $this->failConversation($request, $e);

        // 请求级异常.
        } catch (BrokenRequestException $e) {
            $this->report($e);

            return $this->failRequest($request, $e);
        }
    }


    protected function failRequest(
        CloneRequest $request,
        CommuneRuntimeException $e
    ) : CloneResponse
    {
        $storage = $this->cloner->storage;
        $times = $storage->requestFailTimes ?? 0;
        $times ++;
        if ($times >= $this->cloner->config->maxRequestFailTimes) {
            return $this->failConversation($request, $e);
        }

        $storage->requestFailTimes = $times;

        // 不记录本次请求状态.
        $this->cloner->noConversationState();
        return $request->output(new RequestFailInt($e->getMessage()));
    }

    /**
     * 重置 Session 的异常次数.
     */
    protected function resetFailureCount() : void
    {
        $storage = $this->cloner->storage;
        $storage->requestFailTimes = 0;
    }

    protected function failConversation(
        CloneRequest $request,
        CommuneRuntimeException $e
    ) : CloneResponse
    {

        $message = new SessionFailInt(
            $e->getMessage()
        );

        $this->resetFailureCount();
        $this->cloner->output($this->cloner->input->output($message));
        $this->cloner->endConversation();

        return $request->success($this->cloner);
    }


    /**
     * 上报异常.
     * @param \Throwable $e
     */
    protected function report(\Throwable $e) : void
    {
        $reporter = $this->cloner->container->get(ExceptionReporter::class);
        $reporter->report($e);
    }

    /**
     * 运行多轮对话内核逻辑.
     *
     * @param CloneRequest $request
     * @return CloneResponse
     */
    protected function runDialogManager(CloneRequest $request) : CloneResponse
    {
        $operator = new OStart($this->cloner);
        $tracer = $this->cloner->runtime->trace;

        try {

            while (isset($operator)) {

                $tracer->record($operator);
                $operator = $operator->tick();
                if ($operator->isTicked()) {
                    break;
                }
            }

            unset($next);

        } catch (CommuneRuntimeException $e) {
            throw $e;

        } catch (\Throwable $e) {
            throw new BrokenRequestException($e->getMessage(), $e);

        } finally {
            // 调试模式下检查运行轨迹.
            if (CommuneEnv::isDebug()) {
                $tracer->log($this->cloner->logger);
            }
        }

        return $request->success($this->cloner);
    }

}