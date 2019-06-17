<?php


namespace Commune\Chatbot\OOHost\Context\Stages;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Hearing;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Speech;
use Commune\Chatbot\OOHost\Directing\Navigator;

class CallbackStageBuilder implements OnCallbackStage
{
    use StageSpeechTrait;

    /**
     * @var Stage
     */
    protected $stage;

    /**
     * @var bool
     */
    protected $isCallback;

    /**
     * @var Speech
     */
    protected $dialogSpeech;

    public function __construct(Stage $stage)
    {
        $this->stage = $stage;
        $this->isCallback = $stage->isCallback();
        if ($this->isCallback) {
            $this->dialogSpeech = $stage->dialog
                ->say()
                ->withContext($stage->self);
        }
    }

    protected function isAvailable() : bool
    {
        if (!$this->isCallback) {
            return false;
        }
        $navigator = $this->stage->navigator;
        return !isset($navigator);
    }

    protected function onStartResult() : Navigator
    {
        return $this->stage->navigator
            ?? $this->stage->dialog->wait();
    }

    public function interceptor(callable $action): OnCallbackStage
    {
        $this->stage->onCallback($action);
        return $this;
    }

    /**
     * @return Hearing
     */
    public function hearing()
    {
        $navigator = $this->stage->navigator;
        if (
            $this->isCallback
            && !isset($navigator)
            && $this->stage->value instanceof Message
        ) {
            return $this->stage->dialog->hear($this->stage->value);
        }

        return new FakeHearing(
            $this->stage->navigator,
            $this->stage->dialog,
            ! $this->isCallback
        );
    }


    public function action(callable $action): Navigator
    {
        if ($this->isCallback) {
            $this->stage->onCallback($action);
            return $this->stage->navigator ?? $this->stage->dialog->missMatch();
        }

        return $this->onStartResult();
    }

    public function next(): Navigator
    {
        if ($this->isCallback) {
            return $this->stage->navigator
                ?? $this->stage->dialog->next();
        }

        return $this->onStartResult();
    }

    public function fulfill(): Navigator
    {
        if ($this->isCallback) {
            return $this->stage->navigator
                ?? $this->stage->dialog->next();
        }

        return $this->onStartResult();
    }

    public function restart(): Navigator
    {
        if ($this->isCallback) {
            return $this->stage->navigator
                ?? $this->stage->dialog->restart();
        }

        return $this->onStartResult();
    }

    public function goStage(
        string $stageName,
        bool $resetPipe = false
    ): Navigator
    {
        if ($this->isCallback) {
            return $this->stage->navigator
                ?? $this->stage->dialog->goStage($stageName, $resetPipe);
        }

        return $this->onStartResult();
    }

    public function repeat(): Navigator
    {
        if ($this->isCallback) {
            return $this->stage->navigator
                ?? $this->stage->dialog->repeat();
        }

        return $this->onStartResult();
    }

    public function goStagePipes(
        array $stages,
        bool $resetPipe = false
    ): Navigator
    {
        if ($this->isCallback) {
            return $this->stage->navigator
                ?? $this->stage->dialog->goStagePipes($stages, $resetPipe);
        }

        return $this->onStartResult();
    }

    public function backward(): Navigator
    {
        if ($this->isCallback) {
            return $this->stage->navigator
                ?? $this->stage->dialog->backward();
        }

        return $this->onStartResult();
    }

    public function rewind(): Navigator
    {
        if ($this->isCallback) {
            return $this->stage->navigator
                ?? $this->stage->dialog->rewind();
        }

        return $this->onStartResult();
    }

    public function missMatch(): Navigator
    {
        if ($this->isCallback) {
            return $this->stage->navigator
                ?? $this->stage->dialog->missMatch();
        }

        return $this->onStartResult();
    }

    public function wait(): Navigator
    {
        if ($this->isCallback) {
            return $this->stage->navigator
                ?? $this->stage->dialog->wait();
        }

        return $this->onStartResult();
    }

    public function quit(): Navigator
    {
        if ($this->isCallback) {
            return $this->stage->navigator
                ?? $this->stage->dialog->quit();
        }

        return $this->onStartResult();
    }

    public function reject(): Navigator
    {
        if ($this->isCallback) {
            return $this->stage->navigator
                ?? $this->stage->dialog->reject();
        }

        return $this->onStartResult();
    }

    public function cancel(): Navigator
    {
        if ($this->isCallback) {
            return $this->stage->navigator
                ?? $this->stage->dialog->cancel();
        }

        return $this->onStartResult();
    }


}