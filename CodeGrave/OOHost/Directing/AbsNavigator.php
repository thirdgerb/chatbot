<?php


namespace Commune\Chatbot\OOHost\Directing;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\History\History;

abstract class AbsNavigator implements Navigator
{

    /**
     * @var Dialog
     */
    protected $dialog;

    /**
     * @var History
     */
    protected $history;

    /**
     * AbsNavigator constructor.
     * @param Dialog $dialog
     */
    public function __construct(Dialog $dialog)
    {
        $this->dialog = $dialog;
        $this->history = $dialog->history;
    }

    /**
     * @return Dialog
     */
    public function getDialog(): Dialog
    {
        return $this->dialog;
    }

    /**
     * @return History
     */
    public function getHistory(): History
    {
        return $this->history;
    }



    abstract public function doDisplay(): ? Navigator;

    /**
     * @return Navigator|null
     * @throws \Commune\Chatbot\OOHost\Exceptions\NavigatorException
     */
    public function display(): ? Navigator
    {

        $name = static::class;
        $len = strlen($name);
        $id = '';
        for($i = $len -1 ; $i > 0 ; $i --) {
            if ($name[$i] == '\\') {
                break;
            }
            $id = $name[$i] . $id;
        }

        // tracking
        $this->dialog->session->tracker->log(
            $id,
            $this->history->currentTask(),
            $this->history->belongsTo
        );

        $result = $this->doDisplay();
        return $result;
    }

    public function startCurrent() : Navigator
    {
        $context = $this->history->getCurrentContext();
        $stage = $this->history->currentTask()->getStage();

        return $context->getDef()
            ->startStage(
                $context,
                $this->dialog,
                $stage
            );
    }

    public function callbackCurrent(Message $message) : Navigator
    {
        $context = $this->history->getCurrentContext();
        $stage = $this->history->currentTask()->getStage();

        return $context->getDef()
            ->callbackStage(
                $context,
                $this->dialog,
                $stage,
                $message
            );
    }

    public function intendToCurrent(Context $callbackValue) : Navigator
    {
        $context = $this->history->getCurrentContext();
        $stage = $this->history->currentTask()->getStage();

        return $context->getDef()
            ->intendToStage(
                $context,
                $this->dialog,
                $stage,
                $callbackValue
            );
    }

    public function fallbackCurrent() : Navigator
    {
        $context = $this->history->getCurrentContext();
        $stage = $this->history->currentTask()->getStage();

        return $context->getDef()
            ->fallbackStage(
                $context,
                $this->dialog,
                $stage
            );
    }
}