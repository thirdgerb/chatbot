<?php


namespace Commune\Chatbot\App\Contexts;


use Commune\Chatbot\App\Messages\QA\Choice;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Chatbot\OOHost\Context\OOContext;
use Commune\Chatbot\OOHost\Context\ContextRegistrar;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * 用于定义标准化的路由模块.
 * 向用户提问, 给出"猜你想问"的建议, 把用户导航到别的语境.
 *
 * @property string[] $routes  导航的路由.
 * @property string $askNeeds 猜你想问的问题.
 * @property string $welcome  欢迎语, 可以不填.
 * @property bool $hearAnyIntent 是否拦截所有的意图, 允许导航走.
 * @property string $domain 主动拦截的意图的所在域.
 * @property string|Context $routeTo
 */
class RouteDef extends OOContext
{
    /**
     * @var string
     */
    const DESCRIPTION = 'routing';

    public function __construct(
        array $routes,
        string $domain = '',
        string $welcome = '',
        string $askNeeds = 'ask.needs',
        bool $hearAnyIntent = true
    )
    {
        parent::__construct(get_defined_vars());
    }

    public static function __depend(Depending $depending): void
    {
    }

    public function sayWelcome(Dialog $dialog): void
    {
        if (!empty($this->welcome)) {
            $dialog->say()
                ->withContext($this)
                ->info($this->welcome);
        }
    }

    /**
     * 自定义退出事件处理.
     * @param Exiting $listener
     */
    public function __exiting(Exiting $listener): void
    {
    }

    protected function getRouteNameToDesc() : array
    {
        $repo = ContextRegistrar::getIns();
        $descriptions = [];
        foreach ($this->routes as $routeName) {
            if ($repo->has($routeName)) {
                $descriptions[$routeName] = $repo->get($routeName)->getDesc();
            }
        }
        return $descriptions;
    }

    public function __onStart(Stage $stage): Navigator
    {
        $dialog = $stage->dialog;
        $this->sayWelcome($dialog);
        return $dialog->goStage('route');
    }

    public function __onRoute(Stage $stage) : Navigator
    {
        $routes = $this->getRouteNameToDesc();
        return $stage->talk(
            $this->askRoute($routes),
            [$this, 'hearRoute']
        );
    }

    public function askRoute(array $routes) : \Closure
    {
        return function (Dialog $dialog) use ($routes) : Navigator {
            $dialog->say()
                ->withContext($this)
                ->askChoose(
                    $this->askNeeds,
                    array_values($routes),
                    null
                );
            return $dialog->wait();

        };
    }

    public function hearRoute(Dialog $dialog, Message $message) : Navigator
    {
        $hearMessage = $dialog->hear($message);

        if ($this->hearAnyIntent) {
            $hearMessage->isAnyIntent(function(Dialog $dialog, IntentMessage $intent){
                $this->routeTo = $intent;
                return $dialog->goStage('redirect');
            });
        }

        if (!empty($this->domain)) {
            $hearMessage->isIntentIn([$this->domain], function(Dialog $dialog, IntentMessage $intent){
                $this->routeTo = $intent;
                return $dialog->goStage('redirect');
            });
        }

        return $hearMessage->isAnswer(function(Dialog $dialog, Choice $choice){
            $answer = $choice->toResult();
            $flip = array_flip($this->getRouteNameToDesc());
            $this->routeTo = $flip[$answer];

            return $dialog->goStage('redirect');
        })
            // miss match
            ->end();
    }

    public function __onRedirect(Stage $stage) : Navigator
    {
        return $stage->replaceTo($this->routeTo);
    }
}