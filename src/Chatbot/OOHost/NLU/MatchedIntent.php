<?php


namespace Commune\Chatbot\OOHost\NLU;


use Illuminate\Support\Collection;

class MatchedIntent
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var Collection
     */
    public $entities;

    /**
     * 不需要float. 百分比 * 100 取整
     * @var int
     */
    public $confidence;

    /**
     * @var bool
     */
    public $highlyPossible;

    /**
     * MatchedIntent constructor.
     * @param string $name
     * @param Collection $entities
     * @param int $confidence
     * @param bool $highlyPossible
     */
    public function __construct(string $name, Collection $entities, int $confidence, bool $highlyPossible)
    {
        $this->name = $name;
        $this->entities = $entities;
        $this->confidence = $confidence;
        $this->highlyPossible = $highlyPossible;
    }


}