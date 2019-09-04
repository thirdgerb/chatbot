<?php


namespace Commune\Chatbot\OOHost\Context\Entities;

use Commune\Chatbot\App\Messages\QA\VbQuestion;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\QA\Answer;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Entity;
use Commune\Chatbot\OOHost\Context\Memory\Memory;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 默认的 entity, 可以与 memory 模块结合.
 */
class PropertyEtt implements Entity
{
    // 默认的问题方法前缀, 方法传入Dialog, 返回值是 void.
    // 参考下面的 askDefaultQuestion
    const QUESTION_METHOD_PREFIX = '__ask';
    // 默认的参数校验方法前缀. 方法传入Dialog和Message, 返回 ? Navigator
    // 参考下面的 validate 方法.
    const ANSWER_VALIDATE_METHOD_PREFIX = '__validate';

    /**
     * @var string
     */
    protected $name;


    /**
     * @var string
     */
    protected $memoryName;

    /**
     * @var string
     */
    protected $memoryKey;

    /**
     * @var mixed
     */
    protected $default;


    /**
     * @var bool
     */
    protected $isOptional;

    /**
     * @var string|Question|callable
     */
    protected $question;

    /**
     * @var callable|null
     */
    protected $validator;

    /**
     * AbsEntity constructor.
     * @param string $name
     * @param string|Question|callable $question
     * @param null $default
     * @param string $memoryName
     * @param string|null $memoryKey
     */
    public function __construct(
        string $name,
        $question = '',
        $default = null,
        string $memoryName = '',
        string $memoryKey = null
    )
    {
        $this->name = $name;
        $this->question = empty($question) ? 'ask.default' : $question;
        $this->default = $default;
        $this->memoryName = $memoryName;
        $this->memoryKey = !empty($memoryKey) ? $memoryKey : $this->name;
        $this->isOptional = isset($default);
    }


    public function asStage(Stage $stageRoute) : Navigator
    {

        // context 定义的 stage 最高优.
        // stage method 存在的时候, stage 使用该方法.
        $stageMethod = Context::STAGE_METHOD_PREFIX . $this->name;
        if (method_exists($stageRoute->self, $stageMethod)) {
            return $stageRoute->self->{$stageMethod}($stageRoute);
        }

        // 如果数据存在, 则走下一步.
        if (isset($stageRoute->self->{$this->name})) {
            return $stageRoute->dialog->next();
        }

        // 默认用entity 自己的 stage 方法
        return $stageRoute
            ->onStart(function(Context $self, Dialog $dialog) {
                $this->askDefaultQuestion($self, $dialog);
                return $dialog->wait();
            })
            ->wait([$this, 'defaultCallback']);
    }


    public function defaultCallback(Dialog $dialog, Message $message) : Navigator
    {
        return $dialog->hear($message)
            // 如果得到了答案
            ->isAnswer(function(Context $self, Dialog $dialog, Answer $answer) {

                $result = $this->validate($self, $dialog, $answer);
                // 不为null 表示校验失败.
                if (isset($result)) {
                    return $result;
                }

                // 赋值
                $this->set($self, $answer->toResult());
                // 进入下一步.
                return $dialog->next();
            })

            // 没有答案的情况.
            ->end(function(Dialog $dialog) : Navigator {
                return $this->defaultBadAnswer($dialog);
            });

    }

    protected function validate(Context $self, Dialog $dialog, Message $message) : ? Navigator
    {
        $method = static::ANSWER_VALIDATE_METHOD_PREFIX . ucfirst($this->name);
        if (method_exists($self, $method)) {
            return $self->{$method}($dialog, $message);
        }

        // 用interceptor 作为一种校验方式.
        if (isset($this->validator)) {
            return $dialog->app
                ->callContextInterceptor($self, $this->validator, $message);
        }

        // 不做任何校验.
        return null;
    }


    protected function defaultBadAnswer(Dialog $dialog) : Navigator
    {
        $dialog->say($this->getSlots())->warning('errors.badAnswer');
        return $dialog->rewind();
    }
    protected function askDefaultQuestion(Context $self, Dialog $dialog) : void
    {
        // 方法存在, 优先用方法来提问.
        $method = static::QUESTION_METHOD_PREFIX . ucfirst($this->name);
        if (method_exists($self, $method)) {
            $self->{$method}($dialog);
            return;
        }

        // question 是callable 对象的情况. 比如function
        if (isset($this->question) && is_callable($this->question)) {
            $dialog->app->call($this->question, ['self'=>$self]);
            return;
        }

        // 如果entity 定义的的直接是 question 实例, 用它来提问.
        if (isset($this->question) && $this->question instanceof Question) {
            // 要 clone, 避免污染.
            $dialog->say($this->getSlots())
                ->withContext($self, $self->getDef()->getEntityNames())
                ->ask(clone $this->question);
            return;
        }

        // 如果都没有, question 是个字符串, 就只好用默认提问.
        $dialog->say($this->getSlots())->ask(new VbQuestion(
            $this->question,
            [],
            null
        ));
    }

    protected function getSlots() : array
    {
        return [
            '%default%' => $this->default,
            '%name%' => $this->name,
        ];
    }

    public function withValidator(callable $validator)
    {
        $this->validator = $validator;
        return $this;
    }

    public function __get($name)
    {
        return $this->{$name};
    }

    public function set(Context $self, $value): void
    {
        if (!empty($this->memoryName)) {
            $this->setMemory($self, $value);
            return;
        }
        $self->setAttribute($this->name, $value);
    }

    public function get(Context $self)
    {
        if (!empty($this->memoryName)) {
            return $this->getMemory($self);
        }

        return $self->getAttribute($this->name) ?? $this->default;
    }

    public function isPrepared(Context $self): bool
    {
        if (!empty($this->memoryName)) {
            return $this->memoryExists($self);
        }
        return $self->hasAttribute($this->name) || $this->isOptional;
    }

    protected function getMemoryObj(Context $self) : Memory
    {
        /**
         * @var Memory $memory
         */
        $memoryDef = $self
            ->getSession()
            ->memoryRepo
            ->getDef($this->memoryName);

        if (!isset($memoryDef)) {
            throw new ConfigureException(
                'context '
                . $self->getName()
                . ' define entity '
                . $this->name
                . ' with memory '
                . $this->memoryName
                . ' which is not defined'
            );
        }

        $memory = $memoryDef->newContext();
        return $memory;
    }

    protected function setMemory(Context $self, $value): void
    {
        $memory = $this->getMemoryObj($self)->toInstance($self->getSession());
        $memory->__set($this->memoryKey, $value);
    }

    protected function getMemory(Context $self)
    {
        $memory = $this->getMemoryObj($self)->toInstance($self->getSession());
        return $memory->__get($this->memoryKey) ?? null;
    }

    protected function memoryExists(Context $self): bool
    {
        $memory = $this->getMemoryObj($self)->toInstance($self->getSession());
        return $memory->__isset($this->memoryKey);
    }


}