<?php


namespace Commune\Chatbot\OOHost\Directing\Stage;


use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\History\History;

class GoStagePipes extends AbsNavigator
{
    /**
     * @var array
     */
    protected $pipes;

    /**
     * @var bool
     */
    protected $reset;

    public function __construct(
        Dialog $dialog,
        History $history,
        array $pipes,
        bool $reset
    )
    {
        $this->reset = $reset;
        $this->pipes = $pipes;
        parent::__construct($dialog, $history);
    }

    public function doDisplay(): ? Navigator
    {
        $pipes = $this->pipes;
        $context = $this->history->getCurrentContext();
        $caller = $context->getDef();

        $history = $this->history;

        // 重置掉当前的管道.
        $pipe = array_pop($pipes);
        $history->goStage($pipe, $this->reset);

        while ($pipe = array_pop($pipes)) {
            if (!$caller->hasStage($pipe)) {
                throw new \InvalidArgumentException(
                    static::class
                    . ' stage '.$pipe
                    . ' not found at '
                    . $context->getName()
                );
            }
            $history->addStage($pipe);
        }

        return $caller->startStage(
            $context,
            $this->dialog,
            $this->history->currentTask()->getStage()
        );
    }


}