<?php


namespace Commune\Chatbot\OOHost\Directing;


use Commune\Chatbot\Blueprint\Conversation\RunningSpy;
use Commune\Chatbot\Framework\Conversation\RunningSpyTrait;
use Commune\Chatbot\Framework\Exceptions\ChatbotLogicException;
use Commune\Chatbot\Framework\Exceptions\ContextFailureException;
use Commune\Chatbot\OOHost\Directing\Backward\Failure;
use Commune\Chatbot\OOHost\Directing\End\EndNavigator;
use Commune\Chatbot\OOHost\Directing\Stage\CallbackStage;
use Commune\Chatbot\Framework\Exceptions\CloseSessionException;
use Commune\Chatbot\OOHost\Exceptions\NavigatorException;
use Commune\Chatbot\OOHost\Exceptions\TooManyRedirectException;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionImpl;

class Director implements RunningSpy
{
    use RunningSpyTrait;

    /**
     * @var SessionImpl
     */
    protected $session;

    /**
     * @var int
     */
    protected $ticks = 0;

    /**
     * @var int
     */
    protected $maxTicks;

    /**
     * @var bool
     */
    protected $heard = true;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * Director constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
        $this->sessionId = $session->sessionId;
        $this->maxTicks = $this->session->hostConfig->maxRedirectTimes;
        static::addRunningTrace($this->sessionId, $this->sessionId);
    }


    /**
     * @throws TooManyRedirectException
     */
    protected function tick() : void
    {
        $this->ticks ++;
        if ($this->ticks > $this->maxTicks) {
            throw new TooManyRedirectException(
                static::class
                . ' ticks '. $this->ticks
                . ' times more than max times '
                . $this->maxTicks
                . ', tracking:'
                . $this->session->tracker
            );
        }
    }

    /**
     * @param Navigator|null $navigator
     * @return bool
     * @throws \Throwable
     */
    public function handle(Navigator $navigator = null) : bool
    {
        $navigator = $navigator ?? new CallbackStage(
            $rootDialog = $this->session->getRootDialog(),
            $rootDialog->currentMessage()
        );

        do {

            $dialog = $navigator->getDialog();

            try {
                $this->tick();

                if (!isset($navigator)) {
                    return $this->heard;
                }

                if ($navigator instanceof EndNavigator) {
                    $this->heard = $navigator->beingHeard();
                }

                $navigator = $navigator->display();

            // 一种特殊的异常, 用于跳脱流程.
            } catch (NavigatorException $e) {
                $navigator = $e->getNavigator();

            // 指定的上下文异常, 可以主动退出语境.
            // 会被 onFailure 捕获, 并可以处理
            } catch (ContextFailureException $e) {
                $this->session->getLogger()->warning($e);
                $navigator = new Failure($dialog);

            // 需要销毁对话, 重新开始的异常.
            /**
             * @see TooManyRedirectException
             * @see \Commune\Chatbot\OOHost\Exceptions\SessionDataNotFoundException
             */
            } catch (CloseSessionException $e) {
                $this->session->getLogger()->error($e);
                return $this->destroy();

            // 对话运行中的逻辑错误, 无法修复.
            // 因此也会终止会话.
            } catch (ChatbotLogicException $e) {
                $this->session->getLogger()->critical($e);
                return $this->destroy();

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


        } while (isset($navigator));

        return $this->heard;
    }

    protected function destroy() : bool
    {
        $this->session
            ->conversation
            ->getSpeech()
            ->error(
                $this->session
                    ->chatbotConfig
                    ->defaultMessages
                    ->systemError
            );
        $this->session->shouldQuit();
        return false;
    }

    public function __destruct()
    {
        static::removeRunningTrace($this->sessionId);
    }


}