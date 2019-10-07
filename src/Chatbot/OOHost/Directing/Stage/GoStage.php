<?php


namespace Commune\Chatbot\OOHost\Directing\Stage;

use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\AbsNavigator;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\History\History;

/**
 * 变更当前stage. 如果 reset 为 true, 会清空整个管道.
 */
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
        string $resetStageName,
        bool $reset
    )
    {
        $this->stageName = $resetStageName;
        $this->reset = $reset;
        parent::__construct($dialog);
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
            $this->stageName,
            $this->reset // 如果重置了, 就要重新检查.
        );
    }


}