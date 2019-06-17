<?php


namespace Commune\Chatbot\OOHost\Directing\Stage;


use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\History\History;

class CallbackStage extends AbsNavigator
{
    protected $callbackValue;

    public function __construct(
        Dialog $dialog,
        History $history,
        $callbackValue = null
    )
    {
        $this->callbackValue = $callbackValue;
        parent::__construct($dialog, $history);
    }


    public function doDisplay(): ? Navigator
    {
        $context = $this->history->getCurrentContext();
        $stage = $this->history->currentTask()->getStage();

        return $context->getDef()
            ->callbackStage(
                $context,
                $this->dialog,
                $stage,
                $this->callbackValue
            );
    }


}