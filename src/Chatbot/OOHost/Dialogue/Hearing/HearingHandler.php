<?php


namespace Commune\Chatbot\OOHost\Dialogue\Hearing;


use Commune\Chatbot\Blueprint\Message\Event\EventMsg;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Messages\ArrayMessage;
use Commune\Chatbot\OOHost\Command\CommandDefinition;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
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
    public $isMatched = false;

    /**
     * @var bool
     */
    public $throw;

    /**
     * @var callable[]
     */
    protected $fallback = [];

    /**
     * @var callable
     */
    protected $defaultFallback;


    protected $calledFallback = false;

    protected $calledDefaultFallback = false;


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
    ): Matcher
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

        $this->isMatched = true;
        return $this->callInterceptor($interceptor);
    }

    public function is(
        string $text,
        callable $interceptor = null
    ): Matcher
    {
        if (isset($this->navigator)) return $this;

        // 避免大小写问题.
        if (strtolower($this->message->getTrimmedText()) == strtolower($text)) {
            $this->isMatched = true;
            return $this->callInterceptor($interceptor);
        }

        return $this;
    }

    public function isEmpty(
        callable $interceptor = null
    ): Matcher
    {
        if (isset($this->navigator)) return $this;

        if ($this->message->isEmpty()) {
            $this->isMatched = true;
            return $this->callInterceptor($interceptor);
        }

        return $this;
    }

    public function hasChoice(
        array $choices,
        callable $interceptor = null
    ): Matcher
    {
        if (isset($this->navigator)) return $this;

        if (!$this->message instanceof Answer) {
            return $this;
        }

        foreach ($choices as $choice) {
            if ($this->message->hasChoice($choice)) {
                $this->isMatched = true;
                return $this->callInterceptor($interceptor);
            }
        }

        return $this;
    }

    public function hasEntity(
        string $entityName,
        callable $interceptor = null
    ): Matcher
    {
        if (isset($this->navigator)) return $this;

        $nlu = $this->dialog->session->nlu;
        $entities = $nlu->getMatchedEntities();

        if (!$entities->isEmpty() && $entities->has($entityName)) {
            $this->isMatched = true;
            return $this->callInterceptor($interceptor);
        }

        return $this;
    }

    public function hasEntityValue(
        string $entityName,
        $expect,
        callable $interceptor = null
    ): Matcher
    {
        if (isset($this->navigator)) return $this;

        $nlu = $this->dialog->session->nlu;
        $entities = $nlu->getMatchedEntities();

        if (
            !$entities->isEmpty()
            && $entities->has($entityName)
            && $entities->get($entityName) == $expect
        ) {
            $this->isMatched = true;
            return $this->callInterceptor($interceptor);
        }

        return $this;
    }

    public function runAnyIntent(
        callable $intentAction = null
    ): Matcher
    {
        return $this->isAnyIntent(function(IntentMessage $intent, Dialog $dialog){
            return $intent->navigate($dialog);
        });
    }

    public function runIntent(
        string $intentName,
        callable $intentAction = null
    ): Matcher
    {
        return $this->isIntent($intentName, function(IntentMessage $intent, Dialog $dialog){
            return $intent->navigate($dialog);
        });
    }

    public function runIntentIn(
        array $intentNames,
        callable $intentAction = null
    ): Matcher
    {
        return $this->isIntentIn($intentNames, function(IntentMessage $intent, Dialog $dialog){
            return $intent->navigate($dialog);
        });
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
    ): Matcher
    {
        if (isset($this->navigator)) return $this;
        $session = $this->dialog->session;
        $intent = $session->getPossibleIntent($intentName);

        // 没有命中.
        if (!isset($intent)) {
            return $this;
        }

        // 命中了.
        return $this->heardIntent($intent, $intentAction);
    }

    public function isFulfillIntent(
        string $intentName,
        callable $intentAction = null
    ): Matcher
    {
        if (isset($this->navigator)) return $this;

        $session = $this->dialog->session;
        $intent = $session->getPossibleIntent($intentName);

        // 没有命中.
        if (!isset($intent)) {
            return $this;
        }

        if ($intent->isPrepared()) {
            // 命中了.
            return $this->heardIntent($intent, $intentAction);
        }

        $this->setNavigator($this->dialog->redirect->dependOn($intent));
        return $this;
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
    ): Matcher
    {
        if (isset($this->navigator)) return $this;

        $session = $this->dialog->session;
        $repo = $session->intentRepo;

        // 获取完整的 intentNames
        $names = [];
        foreach ($intentNames as $intentName) {
            // 允许从前缀里取.
            $names = array_merge($names, $repo->getDefNamesByDomain($intentName));
        }
        //  这里是标准名称了.
        $names = array_unique($names);

        // 校验 message 本身.
        if ($this->message instanceof IntentMessage) {
            $name = $this->message->getName();
            if (in_array($name, $names)) {
                return $this->heardIntent($this->message, $intentAction);
            }

            return $this;
        }

        // 校验 session matched intent
        $matched = $session->getMatchedIntent();
        if (isset($matched) && in_array($matched->getName(), $names)) {
            return $this->heardIntent($matched, $intentAction);
        }

        // 逐个校验. 主动匹配.
        foreach ($names as $name) {
            $this->isIntent($name, $intentAction);
            if (isset($this->navigator)) return $this;
        }

        return $this;
    }


    public function isAnyIntent(
        callable $intentAction = null
    ): Matcher
    {
        if (isset($this->navigator)) return $this;

        if ($this->message instanceof IntentMessage) {
            return $this->heardIntent($this->message, $intentAction);
        }

        $session = $this->dialog->session;
        $matched = $session->getMatchedIntent();
        if (!isset($matched)) {
            return $this;
        }

        return $this->heardIntent(
            $matched,
            $intentAction
        );
    }

    /**
     * 第一次命中意图
     * @param IntentMessage $matched
     * @param callable|null $intentAction
     * @return Hearing
     */
    protected function heardIntent(
        IntentMessage $matched,
        callable $intentAction = null
    ) : Hearing
    {

        $this->isMatched = true;

        // 有拦截的情况
        if (isset($intentAction)) {
            return $this->callInterceptor($intentAction, $matched);
        }

        // intent 自己有navigator 的情况
        $navigator = $matched->navigate($this->dialog);
        if (isset($navigator)) {
            return $this->setNavigator($navigator);
        }

        // 当什么也没发生, 例如placeHolderIntent
        return $this;
    }

    /**
     * @deprecated
     * 放弃的方法. 还可能拿出来用, 先不删
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
        $this->isMatched = true;

        // 如果有拦截器存在, 则执行拦截器
        if (isset($interceptor)) {
            return $this->callInterceptor($interceptor, $message);
        }

        // 没有拦截器时, 就当是一个正常的回调.
        // 逻辑会比较难以理解. 所以最好还是写上interceptor
        $this->setNavigator($this->dialog->repeat());
        return $this;
    }

    public function isTypeOf(
        string $messageType,
        callable $interceptor = null
    ): Matcher
    {
        if (isset($this->navigator)) return $this;

        if ($this->message->getMessageType() === $messageType) {
            $this->isMatched = true;
            return $this->callInterceptor($interceptor);
        }

        return $this;
    }

    public function isChoice(
        $suggestionIndex,
        callable $interceptor = null
    ): Matcher
    {
        if (isset($this->navigator)) return $this;

        if (!$this->message instanceof Answer) {
            return $this;
        }

        if ($this->message->hasChoice($suggestionIndex)) {
            $this->isMatched = true;
            return $this->callInterceptor($interceptor);
        }

        return $this;
    }

    public function feels(
        string $emotionName,
        callable $action = null
    ) : Matcher
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
            $this->isMatched = true;
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
        return $feels->feel($this->dialog->session, $emotionName)    ;
    }

    public function isNegative(callable $action = null): Matcher
    {
        if (isset($this->navigator)) return $this;

        $is = $this->message instanceof Confirmation && ! $this->message->isPositive();
        $is = $is || $this->doFeels(Negative::class);

        if ($is) {
            $this->isMatched = true;
            $this->callInterceptor($action);
        }
        return $this;
    }

    public function isPositive(callable $action = null): Matcher
    {
        if (isset($this->navigator)) return $this;

        $is = $this->message instanceof Confirmation && $this->message->isPositive();
        $is = $is || $this->doFeels(Positive::class);

        if ($is) {
            $this->isMatched = true;
            $this->callInterceptor($action);
        }
        return $this;
    }

    public function isAnswer(callable $interceptor = null): Matcher
    {
        if (isset($this->navigator)) return $this;

        if ($this->message instanceof Answer) {
            $this->isMatched = true;
            return $this->callInterceptor($interceptor);
        }

        return $this;
    }

    public function isInstanceOf(
        string $messageClazz,
        callable $interceptor = null
    ): Matcher
    {
        if (isset($this->navigator)) return $this;

        if (is_a($this->message, $messageClazz, TRUE)) {
            $this->isMatched = true;
            return $this->callInterceptor($interceptor);
        }

        return $this;
    }

    public function isCommand(
        string $signature,
        callable $interceptor = null
    ): Matcher
    {

        if (isset($this->navigator)) return $this;

        $cmdText = $this->message->getCmdText();
        if (empty($cmdText)) {
            return $this;
        }

        $cmd = CommandDefinition::makeBySignature($signature);
        $cmdMessage = IntentMatcher::matchCommand($this->message, $cmd);

        if (isset($cmdMessage)) {
            $this->isMatched = true;
            return $this->callInterceptor($interceptor, $cmdMessage);
        }

        return $this;
    }

    public function pregMatch(
        string $pattern,
        array $keys = [],
        callable $interceptor = null
    ): Matcher
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

        $this->isMatched = true;

        return $this->callInterceptor(
            $interceptor,
            new ArrayMessage($this->message, $data)
        );

    }

    public function hasKeywords(
        array $keywords,
        callable $interceptor = null,
        array $notAny = null
    ): Matcher
    {

        if (empty($keywords)) {
            return $this;
        }

        if (isset($this->navigator)) return $this;

        // 关键字匹配只检查文本类型.
        if (!$this->message instanceof VerboseMsg) {
            return $this;
        }

        $nlu = $this->dialog->session->nlu;
        $incoming = $this->dialog->session->incomingMessage;
        $collection = $nlu->getWords();

        // 分词得到的关键字不为空, 用分词来做
        if (!$collection->isEmpty()) {
            foreach ($keywords as $item) {

                // 或关系
                if (
                    is_array($item)  // 表示同义词
                    && $collection->intersect($item)->isEmpty() //交集为空, 说明不存在
                ) {
                    return $this;
                }

                if (!$collection->contains($item)) {
                    return $this;
                }
            }

            $this->isMatched = true;
            return $this->callInterceptor($interceptor);
        }

        // 最脏的办法, 自己去循环匹配.
        $text = $incoming->getMessage()->getTrimmedText();
        if (IntentMatcher::matchWords($text, $keywords, false)) {
            $this->isMatched = true;
            return $this->callInterceptor($interceptor);
        }

        return $this;
    }

    public function then(callable $interceptor): Hearing
    {
        if (isset($this->navigator)) return $this;

        $matched = $this->isMatched;
        $this->isMatched = false;
        return $matched ? $this->callInterceptor($interceptor) : $this;
    }

    public function fallback(callable $fallback, bool $addToEndNotHead = true): Hearing
    {
        if ($addToEndNotHead) {
            array_push($this->fallback, $fallback);
        } else {
            array_unshift($this->fallback, $fallback);
        }
        return $this;
    }

    public function defaultFallback(callable $defaultFallback): Hearing
    {
        $this->defaultFallback = $defaultFallback ?? $this->defaultFallback;
        return $this;
    }


    public function isEvent(string $eventName, callable $action = null): Matcher
    {
        if (isset($this->navigator)) return $this;

        if ($this->message instanceof EventMsg && $this->message->getEventName() == $eventName) {
            $this->isMatched = true;
            $this->callInterceptor($action);
        }

        return $this;
    }


    public function isEventIn(array $eventName, callable $action = null): Matcher
    {
        if (isset($this->navigator)) return $this;

        if (!$this->message instanceof EventMsg) {
            return $this;
        }

        if (in_array($this->message->getEventName(), $eventName)) {
            $this->isMatched = true;
            $this->callInterceptor($action);
        }

        return $this;
    }

    public function always(callable $callable): Hearing
    {
        // 原有的navigator 也会被reset
        $navigator = $this->dialog->app->callContextInterceptor(
            $this->self,
            $callable,
            $message ?? $this->message
        );

        if (isset($navigator)) {
            $this->setNavigator($navigator);
        }

        return $this;
    }

    public function runFallback(): Hearing
    {
        if ($this->calledFallback) {
            return $this;
        }

        $this->calledFallback = true;
        foreach ($this->fallback as $fallback) {
            $this->callInterceptor($fallback);
        }

        return $this;
    }


    public function runDefaultFallback(): Hearing
    {
        if (isset($this->navigator)) return $this;

        if ($this->calledDefaultFallback) {
            return $this;
        }

        $this->calledDefaultFallback = true;

        // 没有navigator 的话就往后走
        return $this->callInterceptor($this->defaultFallback);
    }


    public function end(callable $defaultFallback = null): Navigator
    {
        // 补加载 component
        if (isset($this->navigator)) return $this->navigator;

        // 如果是 event 消息的话. 当没听到.
        if ($this->message instanceof EventMsg) {
            return $this->navigator = $this->dialog->rewind();
        }

        //
        if (isset($defaultFallback)) {
            $this->defaultFallback($defaultFallback);
        }
        $this->runFallback();
        $this->runDefaultFallback();

        return $this->navigator ?? $this->dialog->missMatch();
    }

    public function todo(callable $todo): ToDoWhileHearing
    {
        $this->isMatched = false;
        return new TodoImpl($this, $todo);
    }


}