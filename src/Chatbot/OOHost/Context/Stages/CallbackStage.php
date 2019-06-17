<?php


namespace Commune\Chatbot\OOHost\Context\Stages;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Directing\Navigator;

class CallbackStage extends AbsStage
{
    public function isStart(): bool
    {
        return false;
    }

    public function isCallback(): bool
    {
        return true;
    }

    public function ifAbsent(): Stage
    {
        return $this;
    }

    public function onStart(callable $interceptor): Stage
    {
        return $this;
    }

    public function onCallback(callable $interceptor): Stage
    {
        $this->callInterceptor($interceptor);
        return $this;
    }

    public function talk(
        callable $talkToUser,
        callable $hearFromUser = null
    ): Navigator
    {
        return $this->wait($hearFromUser);
    }


    public function wait(
        callable $hearMessage
    ): Navigator
    {
        // wait 在接受到空回调的时候, 默认是restart
        if (!$this->value instanceof Message) {
            return $this->dialog->restart();
        }
        $this->callInterceptor($hearMessage);
        return $this->navigator ?? $this->dialog->missMatch();
    }

    public function goStage(string $stageName, bool $resetPipes = false): Navigator
    {
        return $this->dialog->goStage($stageName, $resetPipes);
    }

    public function goStagePipes(array $stages, bool $resetPipes = false): Navigator
    {
        return $this->dialog->goStagePipes($stages, $resetPipes);
    }


    public function sleepTo($to, callable $wake = null): Navigator
    {
        // 检查拦截
        if (isset($this->navigator)) return $this->navigator;

        if (isset($wake)) {
            $this->callInterceptor($wake);
        }
        return $this->navigator ?? $this->dialog->restart();
    }

    public function dependOn(
        $dependency,
        callable $callback = null
    ): Navigator
    {
        if (isset($this->navigator)) return $this->navigator;

        if (isset($callback)) {
            $this->callInterceptor($callback);
        }

        return $this->navigator ?? $this->dialog->next();
    }


    public function yieldTo(
        $to = null,
        callable $wake
    ): Navigator
    {

        if (isset($this->navigator)) return $this->navigator;
        $this->callInterceptor($wake);
        return $this->navigator ?? $this->dialog->wait();
    }


}