<?php


namespace Commune\Chatbot\App\Callables\Actions;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Callables\Action;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class ToNext implements Action
{
    /**
     * @var string[]
     */
    protected $stageNames;

    /**
     * GoStage constructor.
     * @param string[] $stageName
     */
    public function __construct(string ... $stageName)
    {
        $this->stageNames = $stageName;
    }


    public function __invoke(
        Context $self,
        Dialog $dialog,
        Message $message
    ): ? Navigator
    {
        $count = count($this->stageNames);

        if ($count === 0) {
            return $dialog->next();
        } elseif ($count === 1) {
            return $dialog->goStage($this->stageNames[0]);
        }

        return $dialog->goStagePipes($this->stageNames);
    }


}