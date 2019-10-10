<?php


namespace Commune\Chatbot\App\Callables\Actions;


use Commune\Chatbot\OOHost\Context\Callables\Interceptor;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * @deprecated
 *
 * 都整合到了 Redirector
 * @see Redirector
 */
class ToNext implements Interceptor
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
        Dialog $dialog
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