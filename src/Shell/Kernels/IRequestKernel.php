<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Kernels;

use Commune\Chatbot\Exceptions\MessengerReqException;
use Commune\Framework\Blueprint\ReqContainer;
use Commune\Framework\Exceptions\DoneRequestException;
use Commune\Message\Convo\ConvoMsg;
use Commune\Message\Directive\DirectiveMsg;
use Commune\Message\Internal\OutgoingMsg;
use Commune\Message\Reaction\ReactionMsg;
use Commune\Shell\Blueprint\Pipeline\ShellPipe;
use Commune\Shell\Blueprint\Render\Renderer;
use Commune\Shell\Blueprint\Session\ShlSession;
use Commune\Shell\Blueprint\Shell;
use Commune\Shell\Blueprint\Kernels\RequestKernel;
use Commune\Shell\Contracts\ShlRequest;
use Commune\Shell\Contracts\ShlResponse;
use Commune\Shell\Exceptions\RequestException;
use Commune\Support\Pipeline\OnionPipeline;


/**
 * Shell 的请求处理内核.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IRequestKernel implements RequestKernel
{
    /**
     * @var Shell
     */
    protected $shell;

    /**
     * IShellKernel constructor.
     * @param Shell $shell
     */
    public function __construct(Shell $shell)
    {
        $this->shell = $shell;
    }


    public function onRequest(
        ShlRequest $request,
        ShlResponse $response
    ): void
    {
        try {

            // 请求不合法
            if (!$this->validateRequest($request, $response)) {
                return;
            }


            $reqContainer = $this->createReqContainer($request);
            $session = $reqContainer->get(ShlSession::class);
            $session = $this->sendSessionThroughPipes($session);

            $this->handleReplies($session, $response);

            // 完成响应.
            $response->sendResponse();

        // 请求本身可发回的异常
        // 不记录日志. 日志通常应该抛异常的地方记录.
        } catch (RequestException $e) {
            $response->sendFailureResponse($e);

        // 终止流程, 并且不响应的异常.
        // 响应应该提前发送掉.
        } catch (DoneRequestException $e) {
            return;

        // 未预料到的错误.
        } catch (\Throwable $e) {

            $this->shell->getExceptionReporter()->report($e);
            // 发送默认的异常信息.
            $response->sendFailureResponse();

        } finally {

            if (isset($session)) {
                $session->finish();
            }

            if (isset($reqContainer)) {
                $reqContainer->finish();
            }
        }
    }

    protected function validateRequest(
        ShlRequest $request,
        ShlResponse $response
    ) : bool
    {
        if ($request->validate()) {
            return true;
        }

        // todo 记录日志
        $warning = $this->shell
            ->getLogInfo()
            ->shellReceiveInvalidRequest($request->getBrief());
        $this->shell
            ->getLogger()
            ->warning($warning);

        $response->sendRejectResponse();
        return false;
    }

    protected function createReqContainer(ShlRequest $request) : ReqContainer
    {
        $procContainer = $this->shell->getProcContainer();

        // 获取新的请求级实例.
        $reqContainer =  $this->shell
            ->getReqContainer()
            ->newInstance($procContainer);

        // 绑定 request
        $reqContainer->share(ReqContainer::class, $reqContainer);
        $reqContainer->share(ShlRequest::class, $request);

        // 重新 boot 服务.
        $this->shell->bootReqServices($reqContainer);

        return $reqContainer;
    }


    /**
     * 通过管道来运行 Session
     *
     * @param ShlSession $session
     * @return ShlSession
     */
    protected function sendSessionThroughPipes(ShlSession $session) : ShlSession
    {
        $pipes = $this->shell->config->pipeline;

        // 没有管道的话, 直接执行.
        if (empty($pipeline)) {
            return $this->callGhost($session);
        }

        $pipeline = new OnionPipeline($session->container);

        while($pipe = array_shift($pipes)) {
            $pipeline->through($pipe);
        }

        $pipeline->via(ShellPipe::HANDLER);

        // 发送会话
        /**
         * @var ShlSession $session
         */
        $session = $pipeline->send(
            $session,
            function (ShlSession $session): ShlSession {
                return $this->callGhost($session);
            }
        );

        return $session;
    }

    /**
     * 与 Ghost 进行通讯.
     * @param ShlSession $session
     * @return ShlSession
     */
    protected function callGhost(ShlSession $session) : ShlSession
    {
        $messenger = $this->shell->messenger;
        try {

            $replies = $messenger->syncSendIncoming($session->getIncomingMsg());
            $session->reply($replies);

        // 请求失败的话, 应该给出结果.
        } catch (MessengerReqException $e) {
            $session->reply([ $e->getOutgoingMsg() ]);
        }

        return $session;
    }

    protected function handleReplies(ShlSession $session, ShlResponse $response) : void
    {
        $renderer = $this->shell->renderer;
        $buffer = $session->getReplies();

        foreach ($buffer as $outgoingMsg) {
            // 如果发现了不同类的消息.
            if (!$this->validateOutgoing($session, $outgoingMsg)) {
                continue;
            }

            $message = $outgoingMsg->message;

            // 如果是 Convo, 直接 buffer 给 request
            if ($message instanceof ConvoMsg) {
                $response->buffer([$message]);
                continue;
            }

            // 如果是 Reaction, 则需要渲染.
            if ($message instanceof ReactionMsg) {
                $this->renderReaction($renderer, $message, $response);
                continue;
            }


            if ($message instanceof DirectiveMsg) {
                $directive = $directives[$message->getId()] ?? '';
                if (!empty($directive)) {
                    $invoker = $session->container->get($directive);
                    $invoker($session, $message);
                }
            }
        }

    }

    protected function renderReaction(
        Renderer $renderer,
        ReactionMsg $message,
        ShlResponse $response
    ) : void
    {
        $template = $renderer->findTemplate($message->getId());
        // 消息其实也可能为空.
        $messages = isset($template) ? $template->render($message) : [];
        $response->buffer($messages);
    }

    protected function runDirective(
        ShlSession $session,
        DirectiveMsg $message
    ) : void
    {
        $directives = $this->shell->config->directives;
        $directiveId = $message->getId();
        $directive = $directives[$directiveId] ?? '';

        if (!empty($directive)) {
            $invoker = $session->container->get($directive);
            $invoker($session, $message);

        } else {
            $warning = $this->shell->getLogInfo()->directiveNotExists($directiveId);
            $this->shell->getLogger()->warning($warning);
        }
    }

    /**
     * 校验消息是否合法.
     * @param ShlSession $session
     * @param OutgoingMsg $message
     * @return bool
     */
    protected function validateOutgoing(ShlSession $session, OutgoingMsg $message) : bool
    {
        $inScope = $session->getIncomingMsg()->scope;
        $outScope = $message->scope;

        if (
            $inScope->chatId !== $outScope->chatId
            || $inScope->shellName != $outScope->shellName
        ) {
            // todo 记录日志.

            return false;
        }
        return true;
    }


}