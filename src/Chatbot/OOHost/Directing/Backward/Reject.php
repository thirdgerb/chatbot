<?php


namespace Commune\Chatbot\OOHost\Directing\Backward;


use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class Reject extends FallbackNavigator
{

    const EVENT = Definition::REJECTION;

    /**
     * @var string|null
     */
    protected $reason;

    public function __construct(Dialog $dialog, string $reason = null, bool $skipSelfEvent = false)
    {
        parent::__construct($dialog, $skipSelfEvent);
    }

    public function doDisplay(): ? Navigator
    {
        $reason = $this->reason ?? 'errors.mustBeSupervisor';

        if (!empty($reason)) {
            $this->dialog->say()->error($reason);
        }

        return parent::doDisplay();
    }
}