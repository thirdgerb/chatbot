<?php


namespace Commune\Chatbot\Framework\Exceptions;


use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Exceptions\RequestExceptionInterface;
use Commune\Chatbot\Blueprint\Exceptions\RuntimeExceptionInterface;
use Commune\Chatbot\Blueprint\Exceptions\StopServiceExceptionInterface;
use Commune\Chatbot\Contracts\ExceptionHandler;

class FatalExceptionHandler implements ExceptionHandler
{

    /**
     * @var Application
     */
    protected $chatApp;

    /**
     * SimpleExpHandler constructor.
     * @param Application $chatApp
     */
    public function __construct(Application $chatApp)
    {
        $this->chatApp = $chatApp;
    }

    public function handleException(Conversation $conversation, \Throwable $e): Conversation
    {
        // 通报无法响应.
        $conversation->getRequest()->sendFailureResponse();

        // 不需要特别处理, 不响应就ok了.
        if ($e instanceof RequestExceptionInterface) {
            return $conversation;
        }

        // 关闭客户端的严重错误.
        if ($e instanceof RuntimeExceptionInterface) {
            $conversation->onFinish(function() use ($conversation){
                $this->chatApp->getServer()->closeClient($conversation);
            });

        } elseif ($e instanceof StopServiceExceptionInterface) {
            $conversation = $this->catchStopServiceException($conversation, $e);

        } else {
            $e = new FatalErrorException($e);
            $conversation = $this->catchStopServiceException($conversation, $e);
        }

        return $conversation;
    }

    /**
     * 要关闭 worker 的异常.
     *
     * @param Conversation $conversation
     * @param StopServiceExceptionInterface $e
     * @return Conversation
     */
    protected function catchStopServiceException(
        Conversation $conversation,
        StopServiceExceptionInterface $e
    ) : Conversation
    {
        $conversation->onFinish(function() {
            $this->chatApp->setAvailable(false);
            $this->chatApp->getServer()->fail();
        });

        return $conversation;
    }

    public function reportException(
        Conversation $conversation,
        \Throwable $e
    ): void
    {
        $this->logConversationalException($conversation, $e);

        if ($e instanceof RuntimeExceptionInterface) {
            $this->reportRuntimeException($e);
        } elseif ($e instanceof StopServiceExceptionInterface) {
            $this->reportServiceStopException($e);
        } elseif ($e instanceof \ErrorException || $e instanceof \Error) {
            $this->reportServiceStopException(new FatalErrorException($e));
        }
    }

    public function logConversationalException(
        Conversation $conversation,
        \Throwable $e
    ): void
    {
        $conversation->getLogger()->error(strval($e));
    }

    public function reportServiceStopException(
        StopServiceExceptionInterface $e
    ): void
    {
        $this->chatApp->getConsoleLogger()->critical($e);
    }

    public function reportRuntimeException(
        RuntimeExceptionInterface $e
    ): void
    {
        $this->chatApp->getConsoleLogger()->error($e);
    }


}