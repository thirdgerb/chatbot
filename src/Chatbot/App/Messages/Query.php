<?php


namespace Commune\Chatbot\App\Messages;


use Commune\Chatbot\App\Messages\Templates\QuestionTemp;
use Commune\Chatbot\Blueprint\Message\Tags\Conversational;

/**
 * 所有的 question 经过渲染后, 可以统一变成 Query
 * 这样方便 template 进行渲染.
 *
 * @see QuestionTemp
 */
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

    public function __sleep(): array
    {
        return array_merge(parent::__sleep(), ['suggestions']);
    }

    /**
     * @return string[]
     */
    public function getSuggestions(): array
    {
        return $this->suggestions;
    }

    public static function mock()
    {
        return new static('test', ['y', 'n']);
    }

}