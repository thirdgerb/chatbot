<?php


namespace Commune\Chatbot\OOHost\Directing\Stage;

use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\History\History;

class GoStage extends AbsNavigator
{
    /**
     * @var string
     */
    protected $stageName;

    /**
     * @var bool
     */
    protected $reset;


    public function __construct(
        Dialog $dialog,
        History $history,
        string $stageName,
        bool $reset
    )
    {
        $this->stageName = $stageName;
        $this->reset = $reset;
        parent::__construct($dialog, $history);
    }

    public function doDisplay(): ? Navigator
    {
        $context = $this->history->getCurrentContext();
        $caller = $context->getDef();

        if (!$caller->hasStage($this->stageName)) {
            throw new \InvalidArgumentException(
                static::class
                . ' stage '.$this->stageName
                . ' not found at '
                . $context->getName()
            );
        }

        $this->history->goStage($this->stageName, $this->reset);

        return $caller->startStage(
            $context,
            $this->dialog,
            $this->stageName
        );
    }


}