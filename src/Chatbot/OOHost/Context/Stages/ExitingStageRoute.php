<?php


namespace Commune\Chatbot\OOHost\Context\Stages;

use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Directing\Navigator;

class ExitingStageRoute extends AbsStageRoute
{
    /**
     * @var Exiting
     */
    protected $exiting;

    public function __construct(string $name, Exiting $exiting)
    {
        $this->exiting = $exiting;
        $self = $exiting->self;
        $dialog = $exiting->dialog;
        parent::__construct($name, $self, $dialog, null);
    }

    public function defaultNavigator(): Navigator
    {
        // 无关紧要. 真正生效的其实是 Exiting::$navigator
        return $this->dialog->wait();
    }

    public function isStart(): bool
    {
        return false;
    }

    public function isCallback(): bool
    {
        return false;
    }

    public function isFallback(): bool
    {
        return false;
    }

    public function isIntended(): bool
    {
        return false;
    }

    public function isExiting(): bool
    {
        return true;
    }

    public function onExiting(callable $interceptor): Stage
    {
        call_user_func($interceptor, $this->exiting);
        return $this;
    }


}