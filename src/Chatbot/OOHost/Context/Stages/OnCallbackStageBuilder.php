<?php


namespace Commune\Chatbot\OOHost\Context\Stages;


use Commune\Chatbot\OOHost\Dialogue\DialogSpeech;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Directing\Navigator;

class OnCallbackStageBuilder implements OnCallbackStage
{
    use StageSpeechTrait;

    /**
     * @var AbsStageRoute
     */
    protected $stage;

    /**
     * @var bool
     */
    protected $isCallback;

    protected $dialogSpeech;

    protected $slots;

    public function __construct(AbsStageRoute $stage, array $slots)
    {
        $this->stage = $stage;
        $this->slots = $slots;
        $this->isCallback = $this->stage->isCallback();
    }

    protected function getDialogSpeech(): DialogSpeech
    {
        return $this->dialogSpeech
            ?? $this->dialogSpeech = $this->stage->dialog->say($this->slots);
    }


    /**
     * 调用过程是否有效.
     * @return bool
     */
    protected function isAvailable() : bool
    {
        if (!$this->stage->isCallback()) {
            return false;
        }
        $navigator = $this->stage->navigator;
        return !isset($navigator);
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
        // 正常的callback
        if ($this->isAvailable()) {
            return $this->stage->dialog->hear($this->stage->value);
        }

        return new FakeHearing(
            $this->stage->dialog,
            $this->stage->defaultNavigator(),
            $this->stage->isStart()
        );
    }


    public function action(callable $action): Navigator
    {
        $this->stage->onCallback($action);
        return $this->stage->defaultNavigator();
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

    public function restart(): Navigator
    {
        if ($this->isAvailable()) {
            return $this->stage->dialog->restart();
        }

        return $this->stage->defaultNavigator();
    }

    public function goStage(
        string $stageName,
        bool $resetPipe = false
    ): Navigator
    {
        if ($this->isAvailable()) {
            return $this->stage->dialog->goStage($stageName, $resetPipe);
        }

        return $this->stage->defaultNavigator();
    }

    public function goStagePipes(
        array $stages,
        bool $resetPipe = false
    ): Navigator
    {
        if ($this->isAvailable()) {
            return $this->stage->dialog->goStagePipes($stages, $resetPipe);
        }

        return $this->stage->defaultNavigator();
    }

    public function backward(): Navigator
    {
        if ($this->isAvailable()) {
            return  $this->stage->dialog->backward();
        }
        return $this->stage->defaultNavigator();
    }

    public function wait(): Navigator
    {
        if ($this->isAvailable()) {
            return $this->stage->dialog->wait();
        }
        return $this->stage->defaultNavigator();
    }


}