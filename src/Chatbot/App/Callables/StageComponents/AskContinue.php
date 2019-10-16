<?php


namespace Commune\Chatbot\App\Callables\StageComponents;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\OOHost\Context\Callables\HearingComponent;
use Commune\Chatbot\OOHost\Context\Callables\StageComponent;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Components\Predefined\Intents\Loop\BreakInt;
use Commune\Components\Predefined\Intents\Loop\NextInt;
use Commune\Components\Predefined\Intents\Loop\PreviousInt;
use Commune\Components\Predefined\Intents\Loop\RewindInt;

/**
 * 一个 Stage 组件,
 * 用于自动实现多个 stage 的循环.
 * 与 goStagePipes() 不同, 加入了循环的控制功能.
 */
class AskContinue implements StageComponent
{
    const INDEX = 'ask_continue_index';


    /**
     * @var string
     */
    protected $query;

    /**
     * @var string
     */
    protected $default;


    /**
     * @var callable[]
     */
    protected $scripts = [];

    /**
     * 流程的最后一步.
     * @var callable|null
     */
    protected $final;

    /**
     * @var null|callable
     */
    protected $backward;

    /**
     * @var null|callable
     */
    protected $fallback;

    /**
     * @var null|callable
     */
    protected $hearingComponent;

    /**
     * @var null|callable
     */
    protected $helping;

    /**
     * AskContinue constructor.
     * @param callable[] $scripts 需要循环执行的脚本.
     * @param string $query  提示用户继续循环的问题.
     * @param string $default 表示"继续"的其它字符.
     */
    public function __construct(array $scripts, string $query = 'ask.continue', string $default = '.' )
    {
        $this->query = $query;
        $this->default = $default;
        $this->scripts = $scripts;
    }


    public function __invoke(Stage $stage): Navigator
    {
        $index = $stage->self->__get(static::INDEX);
        $index = isset($index) ? intval($index) : 0;
        $max = count($this->scripts) - 1;

        // 已经超过了最后一步.
        if ($index > $max) {
            return $stage->buildTalk()->action($this->goEnd());
        }

        // 创建 stage
        $hearing = $stage
            ->onStart($this->scripts[$index])
            ->buildTalk()
            ->askConfirm($this->query, true)
            ->hearing();


        // help
        if (isset($this->helping)) {
            $hearing = $hearing->onHelp($this->helping);
        }


        // hearing 组件
        if (isset($this->hearingComponent)) {
            $hearing->component($this->hearingComponent);
        }

        // 回调方法
        if (isset($this->fallback)) {
            $hearing->fallback($this->fallback);
        }

        return $hearing
            // 直接到最后一步.
            ->todo($this->goEnd())
                ->isIntent(BreakInt::class)
                ->isChoice(0)
                ->isNegative()

            // 下一步.
            ->todo($this->goNext($index))
                ->is($this->default)
                ->isPositive()
                ->isChoice(1)
                ->isEmpty()

            // 回到上一步.
            ->todo($this->goPrevious( $index))
                ->isChoice(2)
                ->isIntent(PreviousInt::class)

            // 跳过, 直接到下一步.
            ->todo($this->goRewind())
                ->isChoice(3)
                ->isIntent(RewindInt::class)
                // 退出
                ->isNegative()

            // 没有听明白...
            ->otherwise()
            ->end();
    }


    /**
     * 如果有必要的话, 可以主动清空掉 context 缓存的索引.
     * @param Context $self
     */
    public static function clearIndex(Context $self) : void
    {
        $self->__set(static::INDEX, null);
    }

    /**
     * 定义当循环结束的时候, 应该执行的方法
     *
     * @param callable $interceptor
     * @return AskContinue
     */
    public function onFinal(callable $interceptor) : AskContinue
    {
        $this->final = $interceptor;
        return $this;
    }

    /**
     * hearing 的组件. 如果有必要的话.
     *
     * @param callable|HearingComponent $hearingComponent
     * @return AskContinue
     */
    public function onHearing(callable  $hearingComponent ) : AskContinue
    {
        $this->hearingComponent = $hearingComponent;
        return $this;
    }

    /**
     * 可以设置用户寻求帮助时的回答.
     * 为 null 将使用默认的回答.
     *
     * @param callable|null $helping
     * @return AskContinue
     */
    public function onHelp(callable $helping = null) : AskContinue
    {
        $this->helping = $helping ?? function(Dialog $dialog){

            $commands = [
                BreakInt::class,
                NextInt::class,
                PreviousInt::class,
                RewindInt::class,
            ];

            $repo = $dialog->session->intentRepo;

            $descs = array_map(function($cmd) use ($repo){
                return $repo->getDef($cmd)->getDesc();
            }, $commands);

            $dialog->say()->askChooseIntents(
                'ask.needs',
                $descs,
                $commands
            );

            return $dialog->wait();
        };
        return $this;
    }

    /**
     * hearing 组件如果没有任何命中的时候, 应该执行的方法.
     *
     * @param callable $fallback
     * @return AskContinue
     */
    public function onHearingFallback(callable  $fallback) : AskContinue
    {
        $this->fallback = $fallback;
        return $this;
    }

    /**
     * 当循环已经到头, 仍然表示要返回上一步的时候.
     *
     * @param callable $backward
     * @return AskContinue
     */
    public function onBackward(callable $backward) : AskContinue
    {
        $this->backward = $backward;
        return $this;
    }

    protected function goEnd() : callable
    {
        return function (Context $self, Dialog $dialog)  : Navigator {
            static::clearIndex($self);
            return $dialog->app
                ->callContextInterceptor(
                    $self,
                    $this->final ?? Redirector::goNext()
                );
        };

    }


    protected function goNext(int $index) : callable
    {
        return function (Context $self, Dialog $dialog) use ($index) : Navigator {
            $self->__set(static::INDEX, $index + 1);
            return $dialog->repeat();
        };
    }

    protected function goPrevious(int $index) : callable
    {
        return function (Context $self, Dialog $dialog) use ($index) : Navigator {
            // 到头了. 如果不能
            if ($index === 0 ) {
                static::clearIndex($self);
                return $dialog->app
                    ->callContextInterceptor(
                        $self,
                        $this->backward ?? Redirector::goRepeat()
                    );
            }

            $self->__set(static::INDEX, $index - 1);
            return $dialog->repeat();
        };
    }

    protected function goRewind() : callable
    {
        return function(Context $self, Dialog $dialog) : Navigator {
            $self->__set(static::INDEX, 0);
            return $dialog->repeat();
        };

    }

}