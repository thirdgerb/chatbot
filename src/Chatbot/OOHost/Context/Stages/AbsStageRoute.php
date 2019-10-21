<?php


namespace Commune\Chatbot\OOHost\Context\Stages;


use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Session\SessionInstance;

/**
 * Stage 路由
 *
 * @property-read string $name
 * @property-read Context $self
 * @property-read Dialog $dialog
 * @property-read null|Message|Context $value
 * @property Navigator|null $navigator
 */
abstract class AbsStageRoute implements Stage
{
    /**
     * @var string string
     */
    protected $name;

    /**
     * @var Context
     */
    protected $self;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var Dialog
     */
    protected $dialog;

    /**
     * @var null|Navigator
     */
    public $navigator;


    /**
     * AbsCheckPoint constructor.
     * @param string $name
     * @param Context $self
     * @param Dialog $dialog
     * @param Message|Context|null $value
     */
    public function __construct(
        string $name,
        Context $self,
        Dialog $dialog,
        ? Message $value
    )
    {
        $this->name = $name;
        $this->self = $self;
        $this->dialog = $dialog;

        if ($value instanceof SessionInstance) {
            $value = $value->toInstance($this->dialog->session);
        }

        $this->value = $value;
    }

    protected function setNavigator(Navigator $navigator = null)
    {
        $this->navigator = $navigator ?? $this->navigator;
    }

    protected function callInterceptor(? callable $interceptor) : void
    {
        if (isset($this->navigator) || empty($interceptor)) {
            return;
        }

        $this->setNavigator($this->dialog->app->callContextInterceptor(
            $this->self,
            $interceptor,
            $this->value
        ));
    }

    public function component(callable $stageRouteComponent): Navigator
    {
        if (isset($this->navigator)) return $this->navigator;

        return $this->dialog->app->call($stageRouteComponent, [
            static::class => $this,
            Stage::class => $this,
            'stage' => $this
        ]);
    }

    public function onStart(callable $interceptor): Stage
    {
        if ($this->isStart()) {
            $this->callInterceptor($interceptor);
        }
        return $this;
    }

    public function onCallback(callable $interceptor): Stage
    {
        if ($this->isCallback()) {
            $this->callInterceptor($interceptor);
        }
        return $this;
    }

    public function onFallback(callable $interceptor): Stage
    {
        if ($this->isFallback()) {
            $this->callInterceptor($interceptor);
        }
        return $this;
    }

    public function onIntended(callable $interceptor): Stage
    {
        if ($this->isIntended()) {
            $this->callInterceptor($interceptor);
        }
        return $this;
    }

    public function talk(
        callable $talkToUser,
        callable $hearFromUser = null
    ): Navigator
    {
        if (isset($this->navigator)) return $this->navigator;

        if ($this->isStart()) {
            $this->callInterceptor($talkToUser);
        }

        if ($this->isCallback()) {
            $this->callInterceptor($hearFromUser);
        }

        return $this->defaultNavigator();
    }


    public function wait(
        callable $hearMessage
    ): Navigator
    {
        // 检查拦截
        if (isset($this->navigator)) return $this->navigator;

        if ($this->isStart()) {
            return $this->dialog->wait();
        }

        if ($this->isCallback()) {
            $this->callInterceptor($hearMessage);
        }

        // 其它的时候, 默认repeat
        return $this->defaultNavigator();
    }

    public function dependOn(
        $dependency,
        callable $callback = null,
        array $stages = null
    ): Navigator
    {
        if (isset($this->navigator)) return $this->navigator;

        if ($this->isStart()) {
            return $this->dialog->redirect->dependOn($dependency, $stages);
        }

        if ($this->isIntended() ) {
            if (isset($callback)) {
                $this->callInterceptor($callback);
            }
            return $this->navigator ?? $this->dialog->next();
        }

        return $this->defaultNavigator();
    }

    public function sleepTo($to, callable $wake = null): Navigator
    {
        if (isset($this->navigator)) return $this->navigator;

        if ($this->isStart()) {
            return $this->dialog->redirect->sleepTo($to);
        }

        if ($this->isFallback() && isset($wake)) {
            $this->callInterceptor($wake);
            return $this->navigator ?? $this->dialog->next();
        }

        return $this->defaultNavigator();
    }

    public function onSubDialog(
        string $belongsTo,
        callable $rootContextMaker,
        Message $message = null,
        bool $keepAlive = true
    ): SubDialogBuilder
    {
        return new SubDialogBuilderImpl(
            $this,
            $belongsTo,
            $rootContextMaker,
            $message,
            $keepAlive
        );
    }


    public function buildTalk(array $slots = []): OnStartStage
    {
        return new OnStartStageBuilder($this, $slots);
    }

    public function hearing()
    {
        if (isset($this->navigator) || !$this->isCallback()) {
            return new FakeHearing(
                $this->dialog,
                $this->defaultNavigator(),
                $this->isStart()
            );
        }

        return $this->dialog->hear($this->value);
    }


    public function __get($name)
    {
        return $this->{$name};
    }

    public function __isset($name)
    {
        return isset($this->{$name});
    }


    abstract public function defaultNavigator(): Navigator;

}