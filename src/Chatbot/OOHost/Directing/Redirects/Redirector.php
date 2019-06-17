<?php


namespace Commune\Chatbot\OOHost\Directing\Redirects;


use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\History\History;

abstract class Redirector extends AbsNavigator
{

    /**
     * @var Context
     */
    protected $to;


    /**
     * Redirector constructor.
     * @param Dialog $dialog
     * @param History $history
     * @param Context $to
     */
    public function __construct(
        Dialog $dialog,
        History $history,
        Context $to
    )
    {
        $this->to = $to;
        parent::__construct($dialog, $history);
    }

}