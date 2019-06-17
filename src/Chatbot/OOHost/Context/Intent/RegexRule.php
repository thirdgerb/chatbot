<?php


namespace Commune\Chatbot\OOHost\Context\Intent;


class RegexRule
{
    /**
     * @var string
     */
    public $pattern;

    /**
     * matched keys
     * @var string[]
     */
    public $matches;

    /**
     * Regex constructor.
     * @param string $pattern
     * @param string[] $matches
     */
    public function __construct(string $pattern, array $matches = [])
    {
        $this->pattern = $pattern;
        $this->matches = $matches;
    }


}