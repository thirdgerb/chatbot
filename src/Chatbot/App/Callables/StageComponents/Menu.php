<?php


namespace Commune\Chatbot\App\Callables\StageComponents;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\Context\Callables\StageComponent;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrarImpl;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 一个菜单式的组件. 方便做常见的菜单导航.
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
     * @var callable|null
     */
    protected $fallback;

    /**
     * @var callable|null
     */
    protected $redirector;

    /**
     * @var callable|null
     */
    protected $hearingComponent;

    /**
     * @var boolean
     */
    protected $hasDefault = false;

    /**
     * @var int|string|null
     */
    protected $defaultChoice;

    /**
     * Menu constructor.
     * @param string $question
     * @param callable[] $menu   预定义的菜单. 结构是:
     *    [
     *      '字符串作为suggestion' => callable ,
     *      '字符串作为suggestion' => stageName ,
     *      'contextName',
     *    ]
     * @param callable|null $redirector   // 跳转到菜单上context 的方式, 默认是 dependOn, 拿到了结果就认为是回调.
     * @param callable|null $fallback
     * @param callable|null $hearingComponent //可定义组件
     */
    public function __construct(
        string $question,
        array $menu,
        callable $redirector = null,
        callable $fallback = null,
        callable $hearingComponent = null
    )
    {
        $this->question = $question;
        $this->menu = $menu;
        $this->fallback = $fallback;
        $this->redirector = $redirector;
        $this->hearingComponent = $hearingComponent;
    }

    public function hearing(callable $hearing) : Menu
    {
        $this->hearingComponent = $hearing;
        return $this;
    }

    /**
     * @param int|float|string $choice
     * @return Menu
     */
    public function defaultChoice($choice) : Menu
    {
        $this->hasDefault = true;
        $this->defaultChoice = $choice;
        return $this;
    }

    public function __invoke(Stage $stage) : Navigator
    {
        return $stage->talk(function(Dialog $dialog){

            $suggestions = [];
            $repo = $dialog->session->contextRepo;

            foreach ($this->menu as $key => $value) {

                if (is_string($key)) {
                    $suggestions[] = $key;

                } elseif ($repo->hasDef($value)) {
                    $suggestions[] = $repo->getDef($value)->getDesc();
                }
            }

            $dialog->say()
                ->askChoose(
                    $this->question,
                    $suggestions,
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

                // 第一种情况, 就是stage
                if (
                    is_string($value)
                    && method_exists(
                        $self,
                        $method = Context::STAGE_METHOD_PREFIX
                            . ucfirst($value)
                    )
                ) {
                    $hearing->isChoice(
                        $i,
                        function(Dialog $dialog) use ($value) {
                            return $dialog->goStage($value);
                        }
                    );

                // 第二种情况, 是一个context
                } elseif (is_string($value) && $repo->hasDef($value)) {

                    $hearing->isChoice(
                        $i,
                        function(Dialog $dialog) use ($value, $i){
                            return $this->redirect(
                                $value,
                                $dialog,
                                $i
                            );
                        }
                    );

                // 第三种情况, 是callable
                } elseif (is_callable($value)) {
                    $hearing->isChoice($i, $value);

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

                $i ++;
            }

            // 仍然允许按自己的意愿定义.
            if (isset($this->hearingComponent)) {
                $hearing->component($this->hearingComponent);
            }

            return $hearing->end($this->fallback);
        });
    }

    /**
     * @param string $context
     * @param Dialog $dialog
     * @param int $index  选项.
     * @return Navigator
     */
    protected function redirect(
        string $context,
        Dialog $dialog,
        int $index
    ) : Navigator
    {
        if (isset($this->redirector)) {
            return call_user_func_array(
                $this->redirector,
                func_get_args()
            );
        }

        $repo = $dialog->session->intentRepo;

        // 意图用特殊的方式来处理.
        if ($repo->hasDef($context)) {
            $intent = $repo->getDef($context)->newContext();
            return $intent->navigate($dialog);
        }

        // 默认的重定向是 sleepTo
        // 这会导致无法 cancel 掉整个流程.
        return $dialog->redirect->sleepTo($context);
    }

}