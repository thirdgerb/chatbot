<?php


namespace Commune\Chatbot\OOHost\Context\Stages;


use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Dialogue\Redirect;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Session\SessionInstance;

/**
 * Stage 路由
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
    protected $navigator;


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
            $this->value instanceof Message ? $this->value : null
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


    public function replaceTo($to = null, string $level = Redirect::THREAD_LEVEL):Navigator
    {
        return $this->navigator ?? $this->dialog->redirect->replaceTo($to, $level);
    }

    public function buildTalk(array $slots = []): OnStartStage
    {
        return new OnStartStageBuilder($this, $slots);
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

    public function hearing()
    {
        if (isset($this->navigator) || $this->isStart()) {
            return new FakeHearing(
                $this->dialog,
                $this->navigator ?? $this->dialog->wait()
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
}