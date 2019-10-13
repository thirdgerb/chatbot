<?php


namespace Commune\Chatbot\App\Callables\StageComponents;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\Context\Callables\StageComponent;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 一个菜单式的组件. 方便做常见的菜单导航.
 * 原版比较复杂, 越复杂越不利于用户使用, 于是做了简化.
 *
 * - 现在是纯数字索引的菜单选项.
 * - 选项如果是 context, 会 sleepTo 该 context, 返回当前stage时默认执行 repeat
 *
 * 简化的原因, 想通了两个事情:
 * - 用户本来需要简单功能, 功能越多, 反而上手越困难.
 * - Menu 组件做得复杂没有意义, 因为 stage 本身的 api 已经够用了, 反而比 menu 简单.
 *
 */
class Menu implements StageComponent
{
    /**
     * @var string
     */
    protected $question;

    /**
     * @var callable[]  string $suggestion => callable $caller
     */
    protected $menu;

    /**
     * @var boolean
     */
    protected $hasDefault = false;

    /**
     * @var int|string|null
     */
    protected $defaultChoice;

    /**
     * @var callable|null
     */
    protected $hearingComponent;

    /**
     * @var callable|null
     */
    protected $fallback;

    /**
     * Menu constructor.
     * @param string $question
     * @param callable[] $menu   预定义的菜单. 结构是:
     *    [
     *      '字符串作为suggestion' => callable ,
     *      '字符串作为suggestion' => stageName , //推荐使用的方式
     *      'contextName',
     *    ]
     * @param int|null $default
     */
    public function __construct(
        string $question,
        array $menu,
        int $default = null
    )
    {
        $this->question = $question;
        $this->menu = $menu;
        $this->defaultChoice = $default;
        $this->hasDefault = isset($default);
    }

    /**
     * 添加一个 hearing 的组件.
     * @param callable $hearing
     * @return Menu
     */
    public function onHearing(callable $hearing) : Menu
    {
        $this->hearingComponent = $hearing;
        return $this;
    }

    public function onFallback(callable $fallback) : Menu
    {
        $this->fallback = $fallback;
        return $this;
    }

    /**
     * 给选项一个默认值.
     * @param int $choice
     * @return Menu
     */
    public function withDefault(int $choice) : Menu
    {
        $this->hasDefault = true;
        $this->defaultChoice = $choice;
        return $this;
    }

    public function __invoke(Stage $stage) : Navigator
    {
        // 默认跳转到别的 context, 再跳转回来, 执行 repeat
        if (isset($this->fallback)) {
            $stage->onFallback($this->fallback);
        } else {
            $stage->onFallback(Redirector::goRepeat());
        }

        return $stage->talk(function(Dialog $dialog){

            $suggestions = [];
            $repo = $dialog->session->contextRepo;

            $i = 0;
            foreach ($this->menu as $key => $value) {
                $i ++;
                // 第一种情况, 键名是 suggestion
                if (is_string($key)) {
                    $suggestions[$i] = $key;

                // 第二种情况, 键名是整数, 则值是 contextName
                } elseif ($repo->hasDef($value)) {
                    $suggestions[$i] = $repo->getDef($value)->getDesc();
                }
            }

            $dialog->say()
                ->askChoose(
                    $this->question,
                    $suggestions,
                    // 默认第一个值就是默认值.
                    $this->hasDefault
                        ? $this->defaultChoice
                        : array_keys($suggestions)[0]
                );

            return $dialog->wait();

        }, function(Context $self, Dialog $dialog, Message $message){

            $hearing = $dialog->hear($message);
            $repo = $dialog->session->contextRepo;

            $i = 0;
            foreach ($this->menu as $key => $value) {
                $i ++;

                // 第一种情况, 值是一个context
                if (is_string($value) && $repo->hasDef($value)) {
                    $hearing = $hearing->isChoice(
                        $i,
                        function (Dialog $dialog) use ($value) {
                            return $this->redirect(
                                $value,
                                $dialog
                            );
                        }
                    );

                // 第二种情况, 值是stage
                } elseif (
                    is_string($value)
                    && method_exists(
                        $self,
                        $method = Context::STAGE_METHOD_PREFIX
                            . ucfirst($value)
                    )
                ) {
                    $hearing = $hearing->isChoice(
                        $i,
                        function(Dialog $dialog) use ($value) {
                            return $dialog->goStage($value);
                        }
                    );

                // 第三种情况, 是callable
                } elseif (is_callable($value)) {
                    $hearing = $hearing->isChoice($i, $value);

                } else {

                    $error = is_scalar($value)
                        ? gettype($value). ' '. $value
                        : gettype($value);

                    throw new ConfigureException(
                        static::class
                        . ' menu should only be string message(key) to callable value, '
                        . ' or int key to context name,'
                        . 'or string message(key) to stage name, '
                        . $error . ' given'
                    );
                }

            }

            // 仍然允许按自己的意愿定义.
            if (isset($this->hearingComponent)) {
                $hearing->component($this->hearingComponent);
            }

            return $hearing->end();
        });
    }

    /**
     * @param string $context
     * @param Dialog $dialog
     * @return Navigator
     */
    protected function redirect(
        string $context,
        Dialog $dialog
    ) : Navigator
    {
        $repo = $dialog->session->intentRepo;

        // 意图用特殊的方式来处理.
        $navigator = null;
        if ($repo->hasDef($context)) {
            $intent = $repo->getDef($context)->newContext();
            $navigator =  $intent->navigate($dialog);
        }

        // 默认的重定向是 sleepTo
        // 这会导致无法 cancel 掉整个流程.
        return $navigator ?? $dialog->redirect->sleepTo($context);
    }

}