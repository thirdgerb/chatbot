<?php


namespace Commune\Chatbot\OOHost\Context\Listeners;


use Commune\Chatbot\Blueprint\Message\Event\EventMsg;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Messages\ArrayMessage;
use Commune\Chatbot\OOHost\Command\CommandDefinition;
use Commune\Chatbot\OOHost\Context\Callables\Action;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Hearing;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Emotion\Emotion;
use Commune\Chatbot\OOHost\Emotion\Emotions\Negative;
use Commune\Chatbot\OOHost\Emotion\Emotions\Positive;
use Commune\Chatbot\OOHost\Emotion\Feeling;
use Commune\Chatbot\OOHost\Exceptions\NavigatorException;
use Commune\Chatbot\OOHost\Context\Intent\IntentMatcher;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionPipe;
use Commune\Chatbot\Blueprint\Message\QA\Confirmation;

class HearingHandler implements Hearing
{
    /**
     * @var Context
     */
    public  $self;

    /**
     * @var Dialog
     */
    public  $dialog;

    /**
     * @var Message
     */
    public  $message;

    /**
     * @var Navigator|null
     */
    public $navigator;

    /**
     * @var bool
     */
    public $heard = false;

    /**
     * @var bool
     */
    public $throw;

    /**
     * @var callable[]
     */
    protected $fallback = [];

    protected $heardUncaught = true;

    /**
     * HearingHandler constructor.
     * @param Context $self
     * @param Dialog $dialog
     * @param Message $message
     * @param bool $throw
     */
    public function __construct(
        Context $self,
        Dialog $dialog,
        Message $message,
        bool $throw = false
    )
    {
        $this->self = $self;
        $this->dialog = $dialog;
        $this->message = $message;
        $this->throw = $throw;
    }

    protected function getParameters() : array
    {
        $parameters = array_fill_keys(
            $this->message->namesAsDependency(),
            $this->message
        );
        $parameters['self'] = $this->self;
        return $parameters;
    }

    public function middleware(string $sessionPipeName): Hearing
    {
        if (isset($this->navigator)) return $this;

        if (!is_a($sessionPipeName, SessionPipe::class, TRUE)) {
            throw new ConfigureException(
                __METHOD__
                . ' pipe '.$sessionPipeName
                . ' is not subclass of ' . SessionPipe::class
            );
        }

        /**
         * @var SessionPipe $pipe
         */
        $pipe = $this->dialog->app->make($sessionPipeName);

        $missed = false;
        $pipe->handle($this->dialog->session, function(Session $session) use (&$missed) {
            $missed = true;
            return $session;
        });

        if ($missed) {
            return $this;
        }

        $this->setNavigator($this->dialog->wait());
        return $this;
    }


    public function component(callable $hearingComponent): Hearing
    {
        if (isset($this->navigator)) return $this;

        $parameters = $this->getParameters();
        $parameters[Hearing::class] = $this;
        $parameters[static::class] = $this;

        $this->dialog->app->call(
            $hearingComponent,
            $parameters,
            __METHOD__
        );
        return $this;
    }

    public function interceptor(callable $interceptor): Hearing
    {
        return $this->callInterceptor($interceptor);
    }


    protected function callInterceptor(callable $interceptor = null, Message $message = null): Hearing
    {
        if (isset($this->navigator)) return $this;

        if (is_null($interceptor)) {
            return $this;
        }

        return $this->setNavigator($this->dialog->app->callContextInterceptor(
            $this->self,
            $interceptor,
            $message ?? $this->message
        ));
    }

    protected function setNavigator(Navigator $navigator = null) : Hearing
    {
        $this->navigator = $navigator;

        // 性能上反而比跑下去还要快一些.
        if ($this->throw && isset($this->navigator)) {
            throw new NavigatorException($this->navigator);
        }

        return $this;
    }


    public function expect(
        callable $prediction,
        callable $interceptor = null
    ): Hearing
    {
        if (isset($this->navigator)) return $this;

        $predict = $this->dialog->app->callContextPrediction(
            $this->self,
            $prediction,
            $this->message
        );

        if (!$predict) {
            return $this;
        }

        $this->heard = true;
        return $this->callInterceptor($interceptor);
    }

    public function is(
        string $text,
        callable $interceptor = null
    ): Hearing
    {
        if (isset($this->navigator)) return $this;

        if ($this->message->getTrimmedText() == $text) {
            $this->heard = true;
            return $this->callInterceptor($interceptor);
        }

        return $this;
    }

    public function isEmpty(
        callable $interceptor = null
    ): Hearing
    {
        if (isset($this->navigator)) return $this;

        if ($this->message->isEmpty()) {
            $this->heard = true;
            return $this->callInterceptor($interceptor);
        }

        return $this;
    }

    public function hasChoice(
        array $choices,
        callable $interceptor = null
    ): Hearing
    {
        if (isset($this->navigator)) return $this;

        if (!$this->message instanceof Answer) {
            return $this;
        }

        foreach ($choices as $choice) {
            if ($this->message->hasChoice($choice)) {
                $this->heard = true;
                return $this->callInterceptor($interceptor);
            }
        }

        return $this;
    }


    public function isAnyIntent(
        callable $intentAction = null
    ): Hearing
    {
        if (isset($this->navigator)) return $this;

        // 如果消息本身就是 intent, 则视作回调.
        if ($this->message instanceof IntentMessage) {
            return $this->callbackIntent(
                $this->message,
                $intentAction
            );
        }

        // 否则, 视作第一次进入的intent 解析.
        $session = $this->dialog->session;
        $matched = $session->intentRepo->matchPossibleIntent($session);

        if (!isset($matched)) {
            return $this;
        }

        return $this->heardIntent(
            $matched,
            $intentAction
        );
    }

    protected function heardIntent(
        IntentMessage $matched,
        callable $intentAction = null
    ) : Hearing
    {
        $this->heard = true;
        $this->heardUncaught = false;
        if (isset($intentAction)) {
            return $this->callInterceptor($intentAction, $matched);
        }

        return $this->setNavigator($matched->navigate($this->dialog));
    }

    /**
     * 拿到了一个回调的intent
     *
     * @param IntentMessage $message
     * @param callable|null $interceptor
     * @return Hearing
     */
    protected function callbackIntent(
        IntentMessage $message,
        callable $interceptor = null
    ) : Hearing
    {
        $this->heard = true;

        // 如果有拦截器存在, 则执行拦截器
        if (isset($interceptor)) {
            return $this->callInterceptor($interceptor, $message);
        }

        // 没有拦截器时, 就当是一个正常的回调.
        // 逻辑会比较难以理解. 所以最好还是写上interceptor
        $this->setNavigator($this->dialog->repeat());
        return $this;
    }

    /**
     * 主动匹配一个意图
     *
     * @param string $intentName
     * @param callable|null $intentAction
     * @return Hearing
     */
    public function isIntent(
        string $intentName,
        callable $intentAction = null
    ): Hearing
    {
        if (isset($this->navigator)) return $this;

        // 如果消息本身是intent, 则是回调.
        if ($this->message instanceof IntentMessage){
            // 命中当前拦截器.
            if ($this->message->getName() === $intentName) {
                return $this->callbackIntent($this->message, $intentAction);
            }
            // 没命中.
            return $this;
        }


        // 主动匹配.
        $session = $this->dialog->session;
        $intent = $session->intentRepo->matchIntent(
            $intentName,
            $session
        );

        if (!isset($intent)) {
            return $this;
        }

        return $this->heardIntent($intent, $intentAction);
    }

    /**
     * 共享同一个拦截器.
     *
     * @param array $intentNames
     * @param callable|null $intentAction
     * @return Hearing
     */
    public function isIntentIn(
        array $intentNames,
        callable $intentAction = null
    ): Hearing
    {
        if (isset($this->navigator)) return $this;

        $session = $this->dialog->session;
        $repo = $session->intentRepo;

        foreach ($intentNames as $intentName) {

            // 允许从前缀里取.
            $names = $repo->getNamesByDomain($intentName);

            foreach ($names as $name) {

                $this->isIntent($name, $intentAction);
                if (isset($this->navigator)) return $this;
            }
        }
        return $this;
    }

    public function isTypeOf(
        string $messageType,
        callable $interceptor = null
    ): Hearing
    {
        if (isset($this->navigator)) return $this;

        if ($this->message->getMessageType() === $messageType) {
            $this->heard = true;
            return $this->callInterceptor($interceptor);
        }

        return $this;
    }

    public function isChoice(
        $suggestionIndex,
        callable $interceptor = null
    ): Hearing
    {
        if (isset($this->navigator)) return $this;

        if (!$this->message instanceof Answer) {
            return $this;
        }

        if ($this->message->hasChoice($suggestionIndex)) {
            $this->heard = true;
            return $this->callInterceptor($interceptor);
        }

        return $this;
    }

    public function feels(
        string $emotionName,
        callable $action = null
    ): Hearing
    {

        if (isset($this->navigator)) return $this;

        if (!is_a($emotionName, Emotion::class, TRUE)) {
            throw new ConfigureException(
                __METHOD__
                . ' emotionName must be subclass of '. Emotion::class
                . ", $emotionName given"
            );
        }


        if ($this->doFeels($emotionName)) {
            $this->heard = true;
            $this->callInterceptor($action);
        }

        return $this;
    }

    protected function doFeels(string $emotionName) : bool
    {
        /**
         * @var Feeling $feels
         */
        $feels = $this->dialog->app->make(Feeling::class);
        return $feels->feel($this->message, $emotionName)    ;
    }

    public function isNegative(callable $action = null): Hearing
    {
        if (isset($this->navigator)) return $this;

        $is = $this->message instanceof Confirmation && ! $this->message->isPositive();
        $is = $is || $this->doFeels(Negative::class);

        if ($is) {
            $this->heard = true;
            $this->callInterceptor($action);
        }
        return $this;
    }

    public function isPositive(callable $action = null): Hearing
    {
        if (isset($this->navigator)) return $this;

        $is = $this->message instanceof Confirmation && $this->message->isPositive();
        $is = $is || $this->doFeels(Positive::class);

        if ($is) {
            $this->heard = true;
            $this->callInterceptor($action);
        }
        return $this;
    }

    public function isAnswer(callable $interceptor = null): Hearing
    {
        if (isset($this->navigator)) return $this;

        if ($this->message instanceof Answer) {
            $this->heard = true;
            return $this->callInterceptor($interceptor);
        }

        return $this;
    }

    public function isInstanceOf(
        string $messageClazz,
        callable $interceptor = null
    ): Hearing
    {
        if (isset($this->navigator)) return $this;

        if (is_a($this->message, $messageClazz, TRUE)) {
            $this->heard = true;
            return $this->callInterceptor($interceptor);
        }

        return $this;
    }

    public function isCommand(
        string $signature,
        callable $interceptor = null
    ): Hearing
    {

        if (isset($this->navigator)) return $this;

        $cmdText = $this->message->getCmdText();
        if (empty($cmdText)) {
            return $this;
        }

        $cmd = CommandDefinition::makeBySignature($signature);
        $cmdMessage = IntentMatcher::matchCommand($this->message, $cmd);

        if (isset($cmdMessage)) {
            $this->heard = true;
            return $this->callInterceptor($interceptor, $cmdMessage);
        }

        return $this;
    }

    public function pregMatch(
        string $pattern,
        array $keys = [],
        callable $interceptor = null
    ): Hearing
    {
        if (isset($this->navigator)) return $this;

        $data = IntentMatcher::matchRegex(
            $this->message->getTrimmedText(),
            $pattern,
            $keys
        );

        if (!isset($data)) {
            return $this;
        }

        $this->heard = true;

        return $this->callInterceptor(
            $interceptor,
            new ArrayMessage($this->message, $data)
        );

    }

    public function hasKeywords(
        array $keywords,
        callable $interceptor = null,
        array $notAny = null
    ): Hearing
    {
        if (isset($this->navigator)) return $this;

        $text = $this->message->getTrimmedText();

        if (!empty($keywords) && IntentMatcher::matchWords($text, $keywords, false)) {
            $this->heard = true;
            return $this->callInterceptor($interceptor);
        }

        return $this;
    }

    public function heard(callable $interceptor): Hearing
    {
        if ($this->heard && $this->heardUncaught) {
            // heard 只会执行一次.
            $this->heardUncaught = false;
            return $this->callInterceptor($interceptor);
        }
        return $this;
    }

    public function fallback(callable $fallback): Hearing
    {
        if (isset($this->navigator)) return $this;

        $this->fallback[] = $fallback;
        return $this;
    }

    public function isEvent(string $eventName, callable $action = null): Hearing
    {
        if (isset($this->navigator)) return $this;

        if ($this->message instanceof EventMsg && $this->message->getEventName() == $eventName) {
            $this->heard = true;
            $this->callInterceptor($action);
        }

        return $this;
    }


    public function isEventIn(array $eventName, callable $action = null): Hearing
    {
        if (isset($this->navigator)) return $this;

        if (!$this->message instanceof EventMsg) {
            return $this;
        }

        if (in_array($this->message->getEventName(), $eventName)) {
            $this->heard = true;
            $this->callInterceptor($action);
        }

        return $this;
    }


    public function end(callable $fallback = null): Navigator
    {
        if (isset($this->navigator)) return $this->navigator;

        // 如果是消息的话.
        if ($this->message instanceof EventMsg) {
            $this->setNavigator($this->dialog->rewind());
            return $this->navigator;
        }

        if (isset($fallback)) {
            $this->fallback[] = $fallback;
        }

        // 如果要匹配任意意图, 需要手动调用 isAnyIntent

        foreach ($this->fallback as $caller) {
            $this->callInterceptor($caller);
            if(isset($this->navigator)) {
                return $this->navigator;
            }
        }


        return $this->navigator ?? $this->dialog->missMatch();
    }

}