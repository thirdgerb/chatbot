<?php


namespace Commune\Chatbot\OOHost\Context\Stages;


use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Speech;
use Commune\Chatbot\OOHost\Directing\Navigator;

class OnStartStageBuilder implements OnStartStage
{

    use StageSpeechTrait;

    /**
     * @var Stage
     */
    protected $stage;

    /**
     * @var bool
     */
    protected $isStart;

    /**
     * @var Speech
     */
    protected $dialogSpeech;


    public function __construct(Stage $stage)
    {
        $this->stage = $stage;
        $this->isStart = $stage->isStart();
        if ($this->isStart) {
            $this->dialogSpeech = $stage->dialog
                ->say()
                ->withContext($stage->self);
        }
    }

    protected function isAvailable() : bool
    {
        if (!$this->isStart) {
            return false;
        }
        $navigator = $this->stage->navigator;
        return !isset($navigator);
    }

    public function next(): Navigator
    {
        // always
        return $this->stage->dialog->next();
    }

    public function fulfill(): Navigator
    {
        // always
        return $this->stage->dialog->fulfill();
    }

    public function action(callable $action): Navigator
    {
        $this->stage->onStart($action);
        return $this->stage->navigator ?? $this->stage->dialog->next();
    }


    public function interceptor(callable $interceptor): OnStartStage
    {
        $this->stage->onStart($interceptor);
        return $this;
    }

    public function goStage(
        string $name,
        bool $resetPipes = false
    ): Navigator
    {
        // 不管是start 还是 callback, 都会直接执行.
        return $this->stage->navigator
            ?? $this->stage->dialog->goStage($name, $resetPipes);
    }

    public function goStagePipes(
        array $pipes,
        bool $resetPipes = false
    ): Navigator
    {
        // 不管是start 还是 callback, 都会直接执行.
        return $this->stage->navigator
            ?? $this->stage->dialog->goStagePipes($pipes, $resetPipes);
    }

    protected function toCallbackStage() : OnCallbackStage
    {
        return new CallbackStageBuilder($this->stage);
    }


    public function dependOn($to): OnCallbackStage
    {
        if ($this->isAvailable()) {
            $this->stage->onStart(function(Dialog $dialog) use ($to) {
                return $dialog->redirect->dependOn($to);
            });
        }

        return $this->toCallbackStage();
    }

    public function replaceTo($to): OnCallbackStage
    {
        if ($this->isAvailable()) {
            $this->stage->onStart(function(Dialog $dialog) use ($to) {
                return $dialog->redirect->replaceTo($to);
            });
        }

        return $this->toCallbackStage();
    }

    public function sleepTo($to): OnCallbackStage
    {
        if ($this->isAvailable()) {
            $this->stage->onStart(function(Dialog $dialog) use ($to) {
                return $dialog->redirect->sleepTo($to);
            });
        }

        return $this->toCallbackStage();
    }

    public function yieldTo($to): OnCallbackStage
    {
        if ($this->isAvailable()) {
            $this->stage->onStart(function(Dialog $dialog) use ($to) {
                return $dialog->redirect->yieldTo($to);
            });
        }

        return $this->toCallbackStage();
    }

    public function callback(): OnCallbackStage
    {
        if ($this->isAvailable()) {
            $this->stage->onStart(function(Dialog $dialog) {
                return $dialog->wait();
            });
        }

        return $this->toCallbackStage();
    }

    public function toStage(): Stage
    {
        return $this->stage;
    }


}