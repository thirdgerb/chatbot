<?php


namespace Commune\Chatbot\OOHost\Context\Stages;


use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\DialogSpeech;
use Commune\Chatbot\OOHost\Dialogue\DialogSpeechImpl;
use Commune\Chatbot\OOHost\Dialogue\Redirect;
use Commune\Chatbot\OOHost\Directing\Navigator;

class OnStartStageBuilder implements OnStartStage
{
    use StageSpeechTrait;

    /**
     * @var AbsStageRoute
     */
    protected $stage;

    /**
     * @var bool
     */
    protected $isStart;

    protected $slots;

    protected $dialogSpeech;

    public function __construct(AbsStageRoute $stage, array $slots)
    {
        $this->stage = $stage;
        $this->isStart = $stage->isStart();
        $this->slots = $slots;
        if ($this->isStart) {
            $this->dialogSpeech = $stage
                ->dialog
                ->say($slots);
        }
    }

    protected function getDialogSpeech(): DialogSpeech
    {
        return $this->dialogSpeech
            ?? $this->dialogSpeech = $this->stage->dialog->say($this->slots);
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
        if ($this->isAvailable()) {
            return $this->stage->dialog->next();
        }
        return $this->stage->defaultNavigator();
    }

    public function fulfill(): Navigator
    {
        if ($this->isAvailable()) {
            return $this->stage->dialog->fulfill();
        }
        return $this->stage->defaultNavigator();
    }


    public function replaceTo($to = null, string $level = Redirect::THREAD_LEVEL):Navigator
    {
        if ($this->isAvailable()) {
            return $this->stage->dialog->redirect->replaceTo($to, $level);
        }
    }

    public function action(callable $action): Navigator
    {
        if ($this->isAvailable()) {
            return $this
                ->stage
                ->dialog
                ->app
                ->callContextInterceptor(
                    $this->stage->self,
                    $action
                );
        }
        return $this->stage->defaultNavigator();
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
        if ($this->isAvailable()) {
            return $this->stage->dialog->goStage($name, $resetPipes);
        }
        return $this->stage->defaultNavigator();
    }

    public function goStagePipes(
        array $pipes,
        bool $resetPipes = false
    ): Navigator
    {
        if ($this->isAvailable()) {
            return $this->stage->navigator
                ?? $this->stage->dialog->goStagePipes($pipes, $resetPipes);
        }
        return $this->stage->defaultNavigator();
    }

    public function wait() : OnCallbackStage
    {
        return new OnCallbackStageBuilder($this->stage, $this->slots);
    }


    public function dependOn($to): Navigator
    {
        if ($this->isAvailable()) {
            return $this->stage->dialog->redirect->dependOn($to);
        }
        return $this->stage->defaultNavigator();
    }


    public function toStage(): Stage
    {
        return $this->stage;
    }

    public function hearing()
    {
        if ($this->stage->isCallback() && !isset($this->stage->navigator)) {
            return $this->stage->dialog->hear($this->stage->value);
        }

        return new FakeHearing(
            $this->stage->dialog,
            $this->stage->defaultNavigator(),
            $this->stage->isStart()
        );
    }



}