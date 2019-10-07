<?php


namespace Commune\Chatbot\App\Messages;


use Commune\Chatbot\Blueprint\Message\Tags\Conversational;

class Query extends Text implements Conversational
{

    /**
     * @var string[]
     */
    protected $suggestions;

    public function __construct(string $input, array $suggestions = [])
    {
        $this->suggestions = $suggestions;
        parent::__construct($input);
    }

    /**
     * @return string[]
     */
    public function getSuggestions(): array
    {
        return $this->suggestions;
    }



}