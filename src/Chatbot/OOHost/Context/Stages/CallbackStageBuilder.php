<?php


namespace Commune\Chatbot\OOHost\Context\Stages;


use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\DialogSpeech;
use Commune\Chatbot\OOHost\Directing\Navigator;

class CallbackStageBuilder implements OnCallbackStage
{
    use StageSpeechTrait;

    /**
     * @var Stage
     */
    protected $stage;

    /**
     * @var DialogSpeech
     */
    protected $dialogSpeech;

    /**
     * @var bool
     */
    protected $isCallback;

    public function __construct(Stage $stage)
    {
        $this->stage = $stage;
        $this->isCallback = $this->stage->isCallback();
        
        if ($this->isCallback) {
            $this->dialogSpeech = $stage->dialog
                ->say()
                ->withContext($stage->self);
        }
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

    protected function onDefaultResult() : Navigator
    {
        if ($this->stage->isStart()) {
            return $this->stage->navigator
                ?? $this->stage->dialog->wait();
        }

        return $this->stage->navigator
            ?? $this->stage->dialog->repeat();
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

        // 正常的callback
        if (!isset($navigator) && $this->stage->isCallback()) {
            return $this->stage->dialog->hear($this->stage->value);
        }

        return new FakeHearing(
            $this->stage->dialog,
            $this->onDefaultResult()
        );
    }


    public function action(callable $action): Navigator
    {
        $this->stage->onCallback($action);
        return $this->onDefaultResult();

    }

    public function next(): Navigator
    {
        if ($this->isCallback) {
            return $this->stage->navigator
                ?? $this->stage->dialog->next();
        }

        return $this->onDefaultResult();
    }

    public function fulfill(): Navigator
    {
        if ($this->isCallback) {
            return $this->stage->navigator
                ?? $this->stage->dialog->next();
        }

        return $this->onDefaultResult();
    }

    public function restart(): Navigator
    {
        if ($this->isCallback) {
            return $this->stage->navigator
                ?? $this->stage->dialog->restart();
        }

        return $this->onDefaultResult();
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

        return $this->onDefaultResult();
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

        return $this->onDefaultResult();
    }

    public function backward(): Navigator
    {
        if ($this->isCallback) {
            return $this->stage->navigator
                ?? $this->stage->dialog->backward();
        }

        return $this->onDefaultResult();
    }

    public function wait(): Navigator
    {
        if ($this->isCallback) {
            return $this->stage->navigator
                ?? $this->stage->dialog->wait();
        }

        return $this->onDefaultResult();
    }


}