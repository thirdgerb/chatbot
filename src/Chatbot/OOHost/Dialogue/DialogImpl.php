<?php


namespace Commune\Chatbot\OOHost\Dialogue;


use Commune\Chatbot\Blueprint\Conversation\RunningSpy;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Framework\Conversation\RunningSpyTrait;
use Commune\Chatbot\Framework\Exceptions\RuntimeException;
use Commune\Chatbot\OOHost\Context\Context;

use Commune\Chatbot\OOHost\Directing;
use Commune\Chatbot\OOHost\Directing\Navigator;

use Commune\Chatbot\OOHost\History\History;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionData;
use Commune\Chatbot\OOHost\Session\SessionDataIdentity;
use Commune\Chatbot\OOHost\Session\SessionInstance;
use Psr\Log\LoggerInterface;


class DialogImpl implements Dialog, Redirect, App, RunningSpy
{
    use RunningSpyTrait;

    /*--------- components ---------*/

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var Session
     */
    protected $sessionImpl;

    /**
     * @var \Commune\Chatbot\Blueprint\Conversation\Conversation
     */
    protected $conversation;

    /**
     * @var string
     */
    protected $belongsTo;

    /**
     * @var Message
     */
    protected $message;

    /*--------- cached ---------*/

    /**
     * @var History
     */
    protected $history;

    /*--------- construct ---------*/

    public function __construct(
        Session $session,
        History $history,
        Message $message
    )
    {
        $this->message = $message;
        $this->sessionImpl = $session;
        $this->conversation = $session->conversation;
        $this->sessionId = $session->sessionId;
        $this->history = $history;
        $this->belongsTo = $history->belongsTo;
        self::addRunningTrace($this->belongsTo, $this->sessionId);
    }

    /*--------- app ---------*/

    public function get($id)
    {
        return $this->conversation->get($id);
    }

    public function has($id)
    {
        return $this->conversation->has($id);
    }

    public function offsetExists($offset)
    {
        return $this->conversation->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->conversation->has($offset)
            ? $this->conversation->make($offset)
            : null;
    }

    public function offsetSet($offset, $value)
    {
        $this->conversation->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->conversation->offsetUnset($offset);
    }


    public function make(string $abstract, array $parameters = [])
    {
        return $this->conversation->make($abstract, $parameters);
    }

    public function getIf(string $id)
    {
        return $this->has($id) ? $this->get($id) : null;
    }

    public function callContextInterceptor(
        Context $self,
        callable $interceptor,
        Message $message = null
    ): ? Navigator
    {
        $result = $this->callContextCallable(
            $self,
            $interceptor,
            // 当外部不传 message 时, 通常是一个 fallback 的场景.
            // onFallback 的时候也允许使用默认的incoming message
            // 可以真正起到fallback 的效果, 同时又可以让 interceptor 的api 一致化.
            // 避免 message 不存在导致 fatal error 或逻辑谬误
            $message ?? $this->message,
            __METHOD__
        );

        if ($result instanceof Navigator) {
            return $result;

        } elseif (empty($result)) {
            return null;

        } else {
            $this->getLogger()->warning(
                'context ' . $self->getName()
                . ' call interceptor but result is not instance of '
                . Navigator::class
            );
            return null;
        }
    }

    protected function callContextCallable(
        Context $self,
        callable $caller,
        Message $message = null,
        string $method = ''
    )
    {
        // 为message 设计各种依赖.
        $message = $message ?? $this->message;

        // context 的依赖
        $parameters = array_fill_keys(
            $self->namesAsDependency(),
            $self
        );

        // message 的依赖, 优先级高于 context
        $parameters = array_fill_keys(
            $message->namesAsDependency(),
            $message
        ) + $parameters;

        // message 的标准依赖.
        $parameters['message'] = $message;
        $parameters[Message::class] = $message;

        // context 的标准依赖.
        $parameters['self'] = $self;
        $parameters[Context::class] = $self;

        return $this->call($caller, $parameters, $method);
    }

    public function callContextPrediction(
        Context $self,
        callable $prediction,
        Message $message = null
    ): bool
    {
        $result = $this->callContextCallable(
            $self,
            $prediction,
            $message,
            __METHOD__
        );
        return !empty($result);
    }


    /**
     * @param callable $caller
     * @param array $parameters
     * @param string $method
     * @return mixed
     * @throws
     */
    public function call(
        callable $caller,
        array $parameters = [],
        string $method = ''
    )
    {
        $parameters = $parameters + $this->defaultDependencies();

        // 还可以用 $dependencies 获取所有可用依赖的名称.
        $parameters[Dialog::DEPENDENCIES] = $this->describeParameters($parameters);

        return $this->conversation->call($caller, $parameters);
    }

    protected function describeParameters(array $parameters) : array
    {
        $keys = [];
        foreach ($parameters as $key => $parameter) {
            if (is_object($parameter)) {
                $keys[$key] = get_class($parameter);
            } else {
                $keys[$key] = gettype($parameter);
            }
        }
        return $keys;
    }


    protected function callMethodError(
        string $method,
        array $parameters,
        \Throwable $e
    ) : RuntimeException
    {
        return new RuntimeException(
            'dialog call method '
            . $method . ' fail, available parameters are: '
            . implode(', ', array_keys($parameters)),
            $e
        );
    }

    /*--------- context ---------*/

    public function newContext(string $contextName, ...$args): Context
    {
        $def = $this->session->contextRepo->getDef($contextName);
        if (isset($def)) {
            return call_user_func_array([$def, 'newContext'], $args);
        }

        throw new \InvalidArgumentException("context $contextName not found");
    }


    /*--------- current ---------*/

    public function currentContext(): Context
    {
        return $this->history->getCurrentContext();
    }

    public function currentStage(): string
    {
        return $this->history->currentTask()->getStage();
    }

    public function currentQuestion(): ? Question
    {
        $question = $this->history->currentQuestion();
        // question 可以是一个session instance. 比如 askIntent
        if ($question instanceof SessionInstance) {
            $question->toInstance($this->session);
        }
        return $question;
    }

    public function currentMessage(): Message
    {
        return $this->message;
    }


    /*--------- talk ---------*/

    public function say(array $slots = []): DialogSpeech
    {
        return new DialogSpeechImpl($this, $slots);
    }


    public function reply(Message $message): void
    {
        // 如果是question, 则记录到 breakpoint里, 方便回溯.
        if ($message instanceof Question) {
            $this->history->setQuestion($message);
        }
        $this->conversation->reply($message);
    }

    /*--------- dialog navigator ---------*/

    public function hear(Message $message = null): Hearing
    {
        $message = $message ?? $this->session->incomingMessage->message;
        $context =$this->history->getCurrentContext();

        /**
         * @var Hearing $hearing
         */
        $hearing = $this->make(Hearing::class, [
            'context' => $context,
            'dialog' => $this,
            'message' => $message
        ]);

        // 注册系统默认的fallback.
        $defaultFallback = $this->session->hostConfig->hearingFallback;
        if (!empty($defaultFallback)) {
            // 允许是类名. 不过实例应该是 callable
            if (
                is_string($defaultFallback)
                && !is_callable($defaultFallback)
                && class_exists($defaultFallback)
            ) {
                $defaultFallback = $this->app->make($defaultFallback);
            }
            $hearing->defaultFallback($defaultFallback);
        }


        // 运行 __hearing 的component
        $method = Context::HEARING_MIDDLEWARE_METHOD;
        if (method_exists($context, $method)) {
            call_user_func([$context, $method], $hearing);
        }

        return $hearing;
    }


    public function fulfill(): Navigator
    {
        return new Directing\Backward\Fulfill($this);
    }


    public function restart(): Navigator
    {
        return $this->goStage(
            Context::INITIAL_STAGE,
            true
        );
    }

    public function next(): Navigator
    {
        return new Directing\Stage\NextStage($this);
    }


    public function goStage(string $stageName, $resetPipe = false): Navigator
    {
        return new Directing\Stage\GoStage(
            $this,
            $stageName,
            $resetPipe
        );
    }

    public function repeat(): Navigator
    {
        return new Directing\Reset\Repeat($this);
    }


    public function goStagePipes(array $stages, $resetPipe = false): Navigator
    {
        if (empty($stages)) {
            throw new \InvalidArgumentException(
                __METHOD__
                . ' argument stages should not be empty'
            );
        }

        return new Directing\Stage\GoStagePipes(
            $this,
            $stages,
            $resetPipe
        );
    }

    public function backward(): Navigator
    {
        return new Directing\Backward\Backward($this);
    }

    public function rewind(): Navigator
    {
        return new Directing\Reset\Rewind($this);
    }

    public function missMatch(): Navigator
    {
        return new Directing\End\MissMatch($this);
    }

    public function wait(): Navigator
    {
        return new Directing\End\Wait($this);
    }

    /*--------- backward ---------*/

    public function quit(bool $skipSelfExitingEvent = false): Navigator
    {
        return new Directing\Backward\Quit($this, $skipSelfExitingEvent);
    }

    public function reject(string $reason = null, bool $skipSelfExitingEvent = false): Navigator
    {
        return new Directing\Backward\Reject($this, $reason, $skipSelfExitingEvent);
    }

    public function cancel(bool $skipSelfExitingEvent = false): Navigator
    {
        return new Directing\Backward\Cancel($this, $skipSelfExitingEvent);
    }

    /*--------- status ---------*/
    public function isCurrent(Context $context): bool
    {
        if (!$context->isInstanced()) {
            $context = $context->toInstance($this->session);
        }

        return $context->getId() === $this->currentContext()->getId();
    }

    public function isDepended(): bool
    {
        return ! is_null($this->isDependedBy());
    }

    public function isDependedBy(): ? string
    {
        $callback = $this->history
            ->getBreakPoint()
            ->process()
            ->currentThread()
            ->callbackTask();

        return isset($callback) ? $callback->getContextId() : null;
    }

    public function findContext(string $id): ? Context
    {
        $datum = $this->session
            ->repo
            ->fetchSessionData(
                $this->session,
                new SessionDataIdentity($id, SessionData::CONTEXT_TYPE)
            );

        return $datum instanceof Context ? $datum : null;
    }


    /*--------- redirect ---------*/

    public function dependOn($dependency, array $stages = null): Navigator
    {
        $dependency = $this->wrapContext($dependency, __METHOD__);
        return new Directing\Redirects\DependOn(
            $this,
            $dependency,
            $stages ?? []
        );
    }


    public function replaceTo(
        $to,
        string $level = Redirect::THREAD_LEVEL,
        string $resetStage = null
    ): Navigator
    {
        $to = $this->wrapContext($to, __METHOD__);
        switch($level) {
            case Redirect::NODE_LEVEL:
                return new Directing\Redirects\ReplaceNodeTo($this,  $to, $resetStage);
            case Redirect::PROCESS_LEVEL:
                return new Directing\Redirects\ReplaceProcessTo($this, $to, $resetStage);
            default:
                return new Directing\Redirects\ReplaceThreadTo($this, $to, $resetStage);
        }
    }

    public function sleepTo($to = null): Navigator
    {
        if (isset($to)) {
            $to = $this->wrapContext($to, __METHOD__);
        }
        return new Directing\Redirects\SleepTo($this,$to);
    }

    public function yieldTo($to = null): Navigator
    {
        if (isset($to)) {
            $to = $this->wrapContext($to, __METHOD__);
        }
        return new Directing\Redirects\YieldTo($this, $to);
    }

    public function home(): Navigator
    {
        return new Directing\Redirects\Home($this);
    }


    /**
     * @param string|Context $context
     * @param string $method
     * @return Context
     */
    protected function wrapContext($context, string $method) : Context
    {
        if ($context instanceof Context) {
            return $context;
        }

        if (is_string($context)) {
            $contextRepo = $this->session->contextRepo;
            $def = $contextRepo->getDef($context);

            if (isset($def)) {
                // 必须是一个不需要参数的 context
                return $def->newContext();
            }

        }

        throw new \InvalidArgumentException(
            $method
            . ' accept $context must be ' . Context::class
            . ' instance, null, or a valid preload context name, '
            . var_export($context) . ' given'
        );
    }

    /*--------- getter ---------*/


    public function getLogger() : LoggerInterface
    {
        return $this->sessionImpl->logger;
    }


    public function __get($name)
    {
        switch ($name) {
            case 'history' :
                return $this->history;
            case 'belongsTo' :
                return $this->belongsTo;
            case 'session' :
                return $this->sessionImpl;
            case 'app' :
                return $this;
            case 'redirect' :
                return $this;
            case 'logger' :
                return $this->getLogger();
            default :
                return null;
        }

    }


    /**
     * 作为依赖注入对象时, 可以使用的名字.
     * @return string[]
     */
    public function defaultDependencies() : array
    {
        $parameters = array_fill_keys([
            static::class,
            Dialog::class,
            'dialog',
            App::class,
            Redirect::class,
        ], $this);

        $parameters[Session::class] = $session = $this->session;
        $parameters[get_class($session)] = $session;
        return $parameters;
    }

    /*--------- subDialog ---------*/

    public function getSubDialog(
        string $belongsTo,
        callable $rootMaker,
        Message $message = null
    ): SubDialog
    {
        $history = new History($this->session, $belongsTo, $rootMaker);
        $message = $message ?? $this->message;

        return new SubDialogImpl(
            $this->session,
            $history,
            $this,
            $message
        );
    }


    public function __destruct()
    {
        self::removeRunningTrace($this->belongsTo);
    }
}