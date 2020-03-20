<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Ghost\Kernels;

use Commune\Chatbot\Blueprint\Exceptions\ChatbotLogicException;
use Commune\Chatbot\Blueprint\Exceptions\CloseSessionException;
use Commune\Chatbot\Blueprint\Exceptions\ContextFailureException;
use Commune\Chatbot\Ghost\Blueprint\Ghost;
use Commune\Chatbot\Ghost\Blueprint\Redirector;
use Commune\Chatbot\Ghost\Blueprint\Session;
use Commune\Chatbot\Ghost\Blueprint\Session\Tracker;

/**
 * 用户的 Kernel. 在接受用户信息时响应.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class UserKernel
{
    /**
     * @var Ghost
     */
    protected $host;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Tracker
     */
    protected $tracker;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * 当前逻辑重定向的次数.
     *
     * @var int
     */
    protected $ticks = 0;

    /**
     * @var int
     */
    protected $maxTicks;

    protected function prepare() : Redirector
    {

    }



    protected function handle() : Session
    {
        try {

            do {
                // 记录每一步的踪迹.
                $this->tracker->record($redirector);
                $redirector = $redirector->invoke($this->host);

            } while (!is_null($redirector));

        // 系统允许的上下文异常, 可以用抛出异常来主动退出语境.
        // 会引发一个名为 Failure 的 Redirector, 并由上级语境捕获, 响应.
        } catch (ContextFailureException $e) {
            $this->session->logger->warning($e);

            //todo failure

        // 主动要求关闭会话的异常.
        } catch (CloseSessionException $e) {
            $this->session->logger->error($e);
            return $this->destorySession();

        // 对话运行中的逻辑错误, 无法修复.
        // 因此也会终止会话.
        } catch (ChatbotLogicException $e) {
            $this->session->logger->critical($e);
            return $this->destroySession();

        // 其它的异常不会捕获. 例如 \RuntimeException
        // 当这些中间环节发生异常时, 可能导致很奇怪的逻辑
        // 所以干脆清除所有回复, 并且 Session 不做任何记录.
        // 因此请尽量不要让 Context 的逻辑中抛出未处理的异常.
        } catch (\Throwable $e) {
            // 清除所有回复.
            // 日志交给外面去记录.
            $this->session->conversation->flushConversationReplies();
            throw $e;
        }

    }


    protected function tick(Redirector $redirector)
    {

    }


}