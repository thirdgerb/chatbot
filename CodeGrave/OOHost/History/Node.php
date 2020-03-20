<?php


namespace Commune\Chatbot\OOHost\History;

use Commune\Chatbot\OOHost\Context\Context;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Arr\ArrayAndJsonAble;

class Node implements ArrayAndJsonAble
{
    use ArrayAbleToJson;

    /**
     * @var string
     */
    protected $contextId;

    /**
     * @var string
     */
    protected $contextName;

    /**
     * @var string
     */
    protected $currentStage;

    /**
     * @var string[]
     */
    protected $stacks = [];

    /**
     * Task constructor.
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->contextId = $context->getId();
        $this->contextName = $context->getName();
        $this->currentStage = Context::INITIAL_STAGE;
    }

    /**
     * @return string
     */
    public function getContextId()  :string
    {
        return $this->contextId;
    }

    /**
     * @return string
     */
    public function getContextName(): string
    {
        return $this->contextName;
    }

    public function getStage() : ? string
    {
        return $this->currentStage;
    }


    public function goStage(string $stageName, bool $reset = false) : void
    {
        if ($reset) {
            $this->stacks = [];
        }
        $this->currentStage = $stageName;
    }


    public function addStage(string $stage)  : void
    {
        array_push($this->stacks, $this->currentStage);
        $this->currentStage = $stage;
    }

    public function nextStage() : ? string
    {
        $stage = array_pop($this->stacks);
        if (isset($stage)) {
            $this->currentStage = $stage;
        }
        return $stage;
    }


    public function toArray(): array
    {
        return [
            'contextId' => $this->contextId,
            'contextName' => $this->contextName,
            'currentStage' => $this->currentStage,
            'stacks' => $this->stacks
        ];
    }

}