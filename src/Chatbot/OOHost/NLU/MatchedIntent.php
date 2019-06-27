<?php


namespace Commune\Chatbot\OOHost\NLU;


class MatchedIntent
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var array
     */
    public $entities;

    /**
     * 不需要float. 百分比 * 100 取整
     * @var int
     */
    public $confidence;

    /**
     * MatchedIntent constructor.
     * @param string $name
     * @param array $entities
     * @param int $confidence
     */
    public function __construct(string $name, array $entities, int $confidence)
    {
        $this->name = $name;
        $this->entities = $entities;
        $this->confidence = $confidence;
    }


}