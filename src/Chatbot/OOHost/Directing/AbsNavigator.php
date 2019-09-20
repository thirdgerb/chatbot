<?php


namespace Commune\Chatbot\OOHost\Directing;


use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Stage\CallbackStage;
use Commune\Chatbot\OOHost\Directing\Stage\GoStage;
use Commune\Chatbot\OOHost\Directing\Stage\StartStage;
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
     * @param History $history
     */
    public function __construct(Dialog $dialog, History $history)
    {
        $this->dialog = $dialog;
        $this->history = $history;
    }


    abstract public function doDisplay(): ? Navigator;

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
        $this->history->tracker->log(
            $id,
            $this->history->currentTask()
        );

        $result = $this->doDisplay();
        return $result;
    }

    public function startCurrent() : Navigator
    {
        return new StartStage($this->dialog, $this->history);
    }

    public function callbackCurrent($callbackValue = null) : Navigator
    {
        return new CallbackStage($this->dialog, $this->history, $callbackValue);
    }
}