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
     * @var string
     */
    protected $stageName;

    /**
     * GoStage constructor.
     * @param string $stageName
     */
    public function __construct(string $stageName = null)
    {
        $this->stageName = $stageName;
    }


    public function __invoke(
        Context $self,
        Dialog $dialog,
        Message $message
    ): ? Navigator
    {
        if (isset($this->stageName)) {
            return $dialog->goStage($this->stageName);
        }
        return $dialog->next();
    }


}