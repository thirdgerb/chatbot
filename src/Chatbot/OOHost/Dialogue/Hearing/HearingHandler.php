<?php


namespace Commune\Chatbot\OOHost\Dialogue\Hearing;


use Commune\Chatbot\Blueprint\Message\EventMsg;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Blueprint\Message\VerbalMsg;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\App\Messages\ArrayMessage;
use Commune\Chatbot\Framework\Utils\OnionPipeline;
use Commune\Chatbot\OOHost\Command\CommandDefinition;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\End\MissMatch;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Emotion\Emotion;
use Commune\Chatbot\OOHost\Emotion\Emotions\Negative;
use Commune\Chatbot\OOHost\Emotion\Emotions\Positive;
use Commune\Chatbot\OOHost\Emotion\Feeling;
use Commune\Chatbot\OOHost\Exceptions\NavigatorException;
use Commune\Chatbot\OOHost\Context\Intent\IntentMatcher;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\NLU\Contracts\EntityExtractor;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionPipe;
use Commune\Chatbot\Blueprint\Message\QA\Confirmation;
use Commune\Components\Predefined\Intents\Dialogue\HelpInt;
use Commune\Support\SoundLike\SoundLikeInterface;

/**
 *
 * @property bool $isMatched
 */
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
     * 是否已经命中了匹配的条件.
     * 只在 to do 的时候用
     * @var bool
     */
    public $matched = false;

    /**
     * 消息已经经过了问题的检查.
     * @var bool
     */
    public $parsedByQuestion  = false;

    /**
     * 如果已经拿到了 navigator, 是抛出异常终止流程, 还是继续往下走.
     * @var bool
     */
    public $throw = false;

    /**
     * @var callable[]
     */
    protected $fallback = [];

    /**
     * 注册的default fallback, 最终一步.
     * @var callable|null
     */
    protected $defaultFallback;

    /**
     * 已经执行过 onHelp 了
     * @var bool
     */
    protected $calledHelp = false;

    /**
     * 已经执行过default fallback
     * @var bool
     */
    protected $calledDefaultFallback = false;

    /**
     * @var Question|null
     */
    protected $question;

    /**
     * components 都在 end 之前执行.
     *
     * @var callable[]
     */
    protected $components = [];

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

    public function middleware(string ...$middleware): Hearing
    {

        foreach ($middleware as $sessionPipeName) {
            if (!is_a($sessionPipeName, SessionPipe::class, TRUE)) {
                throw new ConfigureException(
                    __METHOD__
                    . ' pipe '.$sessionPipeName
                    . ' is not subclass of ' . SessionPipe::class
                );
            }
        }

        $pipeline = new OnionPipeline(
            $this->dialog->session->conversation,
            $middleware
        );

        $touched = false;
        $pipeline->via('handle')->send(
            $this->dialog->session,
            function(Session $session) use (&$touched): Session {
                $touched = true;
                return $session;
            }
        );

        // 如果 session 中途就被返回了, 则 hearing 不再往下执行.
        if (!$touched) {
            $this->setNavigator($this->dialog->wait());
        }

        return $this;
    }


    public function component(callable $hearingComponent): Hearing
    {
        $this->components[] = $hearingComponent;
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

    /*----------- php 条件 -----------*/

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

    public function matchEntity(
        string $entityName,
        callable $action = null
    ): Matcher
    {
        if (isset($this->navigator)) return $this;

        $session = $this->dialog->session;
        $nlu = $session->nlu;
        $exists = false;

        // 如果 NLU 没有匹配到
        if (!$nlu->getMatchedEntities()->has($entityName)) {

            $message = $session->incomingMessage->message;

            // 只有文本才匹配
            if ($message instanceof VerbalMsg) {
                /**
                 * @var EntityExtractor $entityExtractor
                 */
                $entityExtractor = $this->dialog
                    ->app
                    ->make(EntityExtractor::class);

                $matches = $entityExtractor->match($message->getText(), $entityName);
                // 将匹配结果放到 global entities 里
                if (!empty($matches)) {
                    $nlu->mergeEntities([
                        $entityName => $matches
                    ]);
                    $exists = true;
                }
            }
        } else {
            $exists = true;
        }

        if ($exists) {
            $this->isMatched = true;
            $this->callInterceptor($action);
        }

        return $this;
    }

    public function soundLike(
        string $text,
        callable $action = null,
        string $lang = SoundLikeInterface::ZH
    ): Matcher
    {
        if (isset($this->navigator)) return $this;

        if (!$this->message instanceof VerbalMsg) {
            return $this;
        }

        /**
         * @var SoundLikeInterface $soundLike
         */
        $soundLike = $this->dialog->session->conversation->get(SoundLikeInterface::class);
        $input = $this->message->getTrimmedText();

        $result = $soundLike->soundLike(
            $input,
            $text,
            SoundLikeInterface::COMPARE_EXACTLY,
            $lang
        );

        if ($result) {
            $this->isMatched = $result;
            return $this->callInterceptor($action, $this->message);
        }

        return $this;
    }

    public function soundLikePart(
        string $text,
        int $type = SoundLikeInterface::COMPARE_ANY_PART,
        callable $action = null,
        string $lang = SoundLikeInterface::ZH
    ): Matcher
    {
        if (isset($this->navigator)) return $this;

        /**
         * @var SoundLikeInterface $soundLike
         */
        $soundLike = $this->dialog->session->conversation->get(SoundLikeInterface::class);
        $input = $this->message->getTrimmedText();

        $result = $soundLike->soundLike(
            $input,
            $text,
            $type,
            $lang
        );

        if ($result) {
            $this->isMatched = $result;
            return $this->callInterceptor($action, $this->message);
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
        if (!$this->message instanceof VerbalMsg) {
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

    public function isVerbal(
        callable $action = null
    ): Matcher
    {
        return $this->isInstanceOf(VerbalMsg::class, $action);
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


    /*----------- 问答 条件 -----------*/

    protected function getMatchedAnswer() : ? Answer
    {
        // 如果已经匹配过
        // $this->question 存在, 一定匹配过. 反之则不是.
        // 要么就没有问题, 要么没有答案, 要么有答案.
        if (!$this->parsedByQuestion) {

            $this->parsedByQuestion = true;
            // 只有一种可能, 就是默认问题还没parse过.
            $question = $this->dialog->currentQuestion();
            if (isset($question)) {
                $this->question = $question;
                $this->question->parseAnswer($this->dialog->session, $this->message);
            }
        }

        return isset($this->question)
            ? $this->question->getAnswer()
            : (
                $this->message instanceof Answer   // message 自己是 answer 是低优先级.
                ? $this->message
                : null
            );
    }

    public function matchQuestion(Question $question): Matcher
    {
        $question->parseAnswer($this->dialog->session);
        $this->question = $question;
        $this->parsedByQuestion = true;
        return $this;
    }


    public function hasChoice(
        array $choices,
        callable $interceptor = null
    ): Matcher
    {
        if (isset($this->navigator)) return $this;

        $answer = $this->getMatchedAnswer();
        if (!isset($answer)) {
            return $this;
        }

        foreach ($choices as $choice) {
            if ($answer->hasChoice($choice)) {
                $this->isMatched = true;
                return $this->callInterceptor($interceptor, $answer);
            }
        }

        return $this;
    }

    public function isChoice(
        $suggestionIndex,
        callable $interceptor = null
    ): Matcher
    {
        if (isset($this->navigator)) return $this;

        $answer = $this->getMatchedAnswer();
        if (!isset($answer)) {
            return $this;
        }

        if ($answer->hasChoice($suggestionIndex)) {
            $this->isMatched = true;
            return $this->callInterceptor($interceptor, $answer);
        }

        return $this;
    }

    public function isNegative(callable $action = null): Matcher
    {
        if (isset($this->navigator)) return $this;

        $answer = $this->getMatchedAnswer();

        $is = isset($answer)
            && $answer instanceof Confirmation
            && ! $answer->isPositive();

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

        $answer = $this->getMatchedAnswer();

        $is = isset($answer)
            && $answer instanceof Confirmation
            && $answer->isPositive();

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

        $answer = $this->getMatchedAnswer();
        if (isset($answer)) {
            $this->isMatched = true;
            return $this->callInterceptor($interceptor, $answer);
        }

        return $this;
    }


    /*----------- nlu 条件 -----------*/

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

    public function runAnyIntent(): Hearing
    {
        $hearing = $this->isAnyIntent(function(IntentMessage $intent, Dialog $dialog){
            return $intent->navigate($dialog);
        });
        return $hearing;
    }

    public function runIntent(
        string $intentName
    ): Hearing
    {
        return $this->isIntent($intentName, function(IntentMessage $intent, Dialog $dialog){
            $intent->toInstance($dialog->session);
            return $intent->navigate($dialog);
        });
    }

    public function runIntentIn(
        array $intentNames
    ): Hearing
    {
        return $this->isIntentIn($intentNames, function(IntentMessage $intent, Dialog $dialog){
            $intent->toInstance($dialog->session);
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

    public function isPreparedIntent(
        string $intentName,
        callable $whenPrepared = null,
        callable $whenNotPrepared = null
    ): Matcher
    {
        if (isset($this->navigator)) return $this;

        $session = $this->dialog->session;
        $intent = $session->getPossibleIntent($intentName);

        // 没有命中.
        if (!isset($intent)) {
            return $this;
        }

        // 命中了, 并且实现了所有的 Entity
        if ($intent->isPrepared()) {
            return $this->heardIntent($intent, $whenPrepared);
        }

        // 命中了, 但有 Entity 没有获取到, 或许还需要一个多轮对话去获取.
        if (isset($whenNotPrepared)) {
            return $this->heardIntent($intent, $whenNotPrepared);
        }

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

    public function onHelp(callable $helping = null, string $mark = '?'): Matcher
    {
        if ($this->calledHelp) {
            return $this;
        }

        $this->calledHelp = true;
        return $this
            ->is($mark, $helping)
            ->isIntent(HelpInt::class, $helping);
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

    public function runComponent(): Hearing
    {
        while ($component = array_shift($this->components)) {
            call_user_func($component, $this);
        }
        return $this;
    }


    public function runFallback(): Hearing
    {
        while ($fallback = array_shift($this->fallback)) {
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

    public function heardOrMiss(): Navigator
    {
        return $this->navigator ?? new MissMatch($this->dialog);
    }


    public function end(callable $defaultFallback = null): Navigator
    {
        // 运行中间件.
        $this->runComponent();

        // 补加载 component
        if (isset($this->navigator)) return $this->navigator;

        // 如果是 event 消息的话. 当没听到.
        if ($this->message instanceof EventMsg) {
            $this->dialog->session->beSneak();
            return $this->dialog->wait();
        }

        // 如果 help 方法存在, 则执行默认的 help
        if (method_exists($this->self, Context::CONTEXT_HELP_METHOD)) {
            $this->onHelp([$this->self, Context::CONTEXT_HELP_METHOD]);
            if (isset($this->navigator)) return $this->navigator;
        } else {
            $this->onHelp(function(Dialog $dialog){

                $notExists = $dialog
                    ->session
                    ->chatbotConfig
                    ->defaultMessages
                    ->noHelpInfoExists;

                $dialog->say()->warning($notExists);
                return $dialog->rewind();
            });
        }

        // 如果有默认的回调, 先赋值.
        if (isset($defaultFallback)) {
            $this->defaultFallback($defaultFallback);
        }

        $this->runFallback();
        $this->runDefaultFallback();

        return $this->heardOrMiss();
    }

    public function todo(callable $todo): ToDoWhileHearing
    {
        $this->isMatched = false;
        return new TodoImpl($this, $todo);
    }
}