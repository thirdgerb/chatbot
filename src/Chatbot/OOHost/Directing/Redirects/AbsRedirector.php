<?php


namespace Commune\Chatbot\OOHost\Directing\Redirects;


use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Directing\Stage\GoStage;

abstract class AbsRedirector extends AbsNavigator
{

    /**
     * @var Context
     */
    protected $to;

    /**
     * @var string|null
     */
    protected $resetStage;

    /**
     * Redirector constructor.
     * @param Dialog $dialog
     * @param Context $to
     * @param string|null $stage
     */
    public function __construct(
        Dialog $dialog,
        Context $to,
        string $stage = null
    )
    {
        $this->to = $to;
        $this->resetStage = $stage;
        parent::__construct($dialog);
    }

    public function startCurrent(): Navigator
    {
        if (isset($this->resetStage)) {
            return new GoStage(
                $this->dialog,
                $this->resetStage,
                true
            );
        }

        return parent::startCurrent();
    }
}