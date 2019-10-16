<?php


namespace Commune\Chatbot\OOHost\Context\Stages;

use Commune\Chatbot\OOHost\Directing\Navigator;

class CallbackStageRoute extends AbsStageRoute
{
    public function isStart(): bool
    {
        return false;
    }

    public function isCallback(): bool
    {
        return isset($this->value);
    }

    public function isFallback(): bool
    {
        return !isset($this->value);
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
        // 检查拦截
        if (isset($this->navigator)) return $this->navigator;

        // wait 在接受到空回调的时候, 默认是repeat
        if ($this->isFallback()) {
            return $this->dialog->repeat();
        }
        $this->callInterceptor($hearMessage);
        return $this->navigator ?? $this->dialog->missMatch();
    }

    public function sleepTo($to, callable $wake = null): Navigator
    {
        // 检查拦截
        if (isset($this->navigator)) return $this->navigator;

        if ($this->isFallback()) {
            $this->callInterceptor($wake);
        }

        return $this->navigator ?? $this->dialog->restart();
    }

    public function dependOn(
        $dependency,
        callable $callback = null,
        array $stages = null
    ): Navigator
    {
        if (isset($this->navigator)) return $this->navigator;

        if ($this->isCallback()) {
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

        if ($this->isCallback()) {
            $this->callInterceptor($wake);
        }

        return $this->navigator ?? $this->dialog->redirect->yieldTo();
    }


}