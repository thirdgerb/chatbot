<?php


namespace Commune\Chatbot\OOHost\Context\Stages;


use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Redirect;
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


    public function replaceTo($to = null, string $level = Redirect::THREAD_LEVEL):Navigator
    {
        return $this->stage->dialog->redirect->replaceTo($to, $level);
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

    public function wait() : OnCallbackStage
    {
        return new CallbackStageBuilder($this->stage);
    }


    public function dependOn($to): OnCallbackStage
    {
        $this->stage->onStart(function(Dialog $dialog) use ($to) {
            return $dialog->redirect->dependOn($to);
        });
        return $this->wait();
    }


    public function yieldTo($to): OnCallbackStage
    {
        $this->stage->onStart(function(Dialog $dialog) use ($to) {
            return $dialog->redirect->yieldTo($to);
        });

        return $this->wait();
    }

    public function toStage(): Stage
    {
        return $this->stage;
    }


}