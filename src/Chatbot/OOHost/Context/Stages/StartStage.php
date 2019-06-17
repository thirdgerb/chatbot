<?php


namespace Commune\Chatbot\OOHost\Context\Stages;


use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class StartStage extends AbsStage
{

    /**
     * @var bool
     */
    protected $restart;

    /**
     * StartStage constructor.
     * @param string $name
     * @param Context $self
     * @param Dialog $dialog
     */
    public function __construct(string $name, Context $self, Dialog $dialog)
    {
        parent::__construct($name, $self, $dialog, null);
    }

    public function onStart(callable $interceptor): Stage
    {
        if ($this->restart) {
            return $this;
        }
        $this->callInterceptor($interceptor);
        return $this;
    }

    public function onCallback(callable $interceptor): Stage
    {
        return $this;
    }

    public function ifAbsent(): Stage
    {
        if ($this->self->__isset($this->name)) {
            $this->navigator = $this->dialog->next();
        }
        return $this;
    }

    public function talk(
        callable $talkToUser,
        callable $hearFromUser = null
    ): Navigator
    {
        $this->callInterceptor($talkToUser);
        return $this->navigator ?? $this->dialog->wait();
    }


    public function wait(
        callable $hearMessage
    ): Navigator
    {
        // 检查拦截
        if (isset($this->navigator)) return $this->navigator;
        return $this->dialog->wait();
    }

    public function dependOn(
        $dependency,
        callable $callback = null
    ): Navigator
    {
        if (isset($this->navigator)) return $this->navigator;

        return $this->dialog->redirect->dependOn($dependency);
    }


    public function sleepTo(
        $to,
        callable $wake = null
    ): Navigator
    {
        // 检查拦截
        if (isset($this->navigator)) return $this->navigator;

        return $this->dialog->redirect->sleepTo($to);
    }

    public function yieldTo(
        $to = null,
        callable $wake
    ): Navigator
    {
        if (isset($this->navigator)) return $this->navigator;

        return $this->dialog->redirect->yieldTo($to);
    }

    public function goStage(string $stageName, bool $resetPipes = false): Navigator
    {
        return $this->dialog->wait();
    }

    public function goStagePipes(array $stages, bool $resetPipes = false): Navigator
    {
        return $this->dialog->wait();
    }


    public function isStart(): bool
    {
        return true;
    }

    public function isCallback(): bool
    {
        return false;
    }

}