<?php


namespace Commune\Chatbot\OOHost\Dialogue;


use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Framework\Exceptions\RuntimeException;
use Commune\Chatbot\OOHost\Context\ContextRegistrar;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Hearing;
use Commune\Chatbot\OOHost\Context\Listeners\HearingHandler;

use Commune\Chatbot\OOHost\Directing;
use Commune\Chatbot\OOHost\Directing\Navigator;

use Commune\Chatbot\OOHost\History\History;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionImpl;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Log\LoggerInterface;


/**
 * @property-read App $app
 * @property-read Speech $talk
 * @property-read Session $session
 * @property-read Redirect $redirect
 * @property-read LoggerInterface $logger
 *
 */
class DialogImpl implements Dialog, Redirect, App
{
    // use TalkTrait;

    /*--------- components ---------*/

    /**
     * @var Session
     */
    protected $sessionImpl;

    protected $conversation;

    /*--------- cached ---------*/

    /**
     * @var History
     */
    protected $history;

    /*--------- construct ---------*/

    public function __construct(SessionImpl $session, History $history)
    {
        $this->sessionImpl = $session;
        $this->conversation = $session->conversation;
        $this->history = $history;
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
            $message,
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
        $parameters  = [];

        // 为message 设计各种依赖.
        if (isset($message)) {
            $parameters = array_fill_keys(
                $message->namesAsDependency(),
                $message
            );
            $parameters['message'] = $message;
            $parameters[Message::class] = $message;
        }

        $parameters['self'] = $self;
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
        $def = ContextRegistrar::getIns()->get($contextName);
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

    public function prevQuestion(): ? Question
    {
        return $this->history->getBreakPoint()->prevQuestion;
    }


    /*--------- talk ---------*/

    public function say(array $slots = []): Speech
    {
        return new DialogSpeech($this, $slots);
    }


    public function reply(Message $message): void
    {
        // 如果是question, 则记录到 breakpoint里, 方便回溯.
        if ($message instanceof Question) {
            $this->history->getBreakPoint()->question = $message;
        }
        $this->conversation->reply($message);
    }

    /*--------- dialog navigator ---------*/

    public function hear(Message $message): Hearing
    {
        $hearing = new HearingHandler(
            $context =$this->history->getCurrentContext(),
            $this,
            $message
        );

        $method = Context::HEARING_MIDDLEWARE_METHOD;
        if (method_exists($context, $method)) {
            $context->{$method}($hearing);
        }

        return $hearing;
    }


    public function fulfill(): Navigator
    {
        return new Directing\Backward\Fulfill($this, $this->history);
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
        return new Directing\Stage\NextStage($this, $this->history);
    }


    public function goStage(string $stageName, $resetPipe = false): Navigator
    {
        return new Directing\Stage\GoStage(
            $this,
            $this->history,
            $stageName,
            $resetPipe
        );
    }

    public function repeat(): Navigator
    {
        return new Directing\Dialog\Repeat($this, $this->history);
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
            $this->history,
            $stages,
            $resetPipe
        );
    }

    public function backward(): Navigator
    {
        return new Directing\Backward\Backward($this, $this->history);
    }

    public function rewind(): Navigator
    {
        return new Directing\Dialog\Repeat($this, $this->history);
    }

    public function missMatch(): Navigator
    {
        return new Directing\Dialog\MissMatch($this, $this->history);
    }

    public function wait(): Navigator
    {
        return new Directing\Dialog\Wait($this, $this->history);
    }

    /*--------- backward ---------*/

    public function quit(bool $skipSelfExitingEvent = false): Navigator
    {
        return new Directing\Backward\Quit($this, $this->history, $skipSelfExitingEvent);
    }

    public function reject(bool $skipSelfExitingEvent = false): Navigator
    {
        return new Directing\Backward\Reject($this, $this->history, $skipSelfExitingEvent);
    }

    public function cancel(bool $skipSelfExitingEvent = false): Navigator
    {
        return new Directing\Backward\Cancel($this, $this->history, $skipSelfExitingEvent);
    }


    /*--------- redirect ---------*/

    public function dependOn($dependency): Navigator
    {
        $dependency = $this->wrapContext($dependency, __METHOD__);
        return new Directing\Redirects\DependOn(
            $this,
            $this->history,
            $dependency
        );
    }


    public function replaceTo($to, string $level = Redirect::THREAD_LEVEL): Navigator
    {
        $to = $this->wrapContext($to, __METHOD__);
        switch($level) {
            case Redirect::NODE_LEVEL:
                return new Directing\Redirects\ReplaceNodeTo($this, $this->history, $to);
            case Redirect::PROCESS_LEVEL:
                return new Directing\Redirects\ReplaceProcessTo($this, $this->history, $to);
            default:
                return new Directing\Redirects\ReplaceThreadTo($this, $this->history, $to);
        }
    }

    public function sleepTo($to): Navigator
    {
        $to = $this->wrapContext($to, __METHOD__);
        return new Directing\Redirects\SleepTo($this, $this->history, $to);
    }

    public function yieldTo($to): Navigator
    {
        $to = $this->wrapContext($to, __METHOD__);
        return new Directing\Redirects\YieldTo($this, $this->history, $to);
    }

    public function home(): Navigator
    {
        return new Directing\Redirects\Home($this, $this->history);
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
            $def = ContextRegistrar::getIns()->get($context);
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
        return $this->session->logger;
    }


    public function __get($name)
    {
        switch ($name) {
            case 'session' :
                return $this->sessionImpl;
            case 'app' :
                return $this;
            case 'talk' :
                return $this->say();
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

    public function __destruct()
    {
        if (CHATBOT_DEBUG) {
            $this->getLogger()->debug(__METHOD__);
        }
    }
}