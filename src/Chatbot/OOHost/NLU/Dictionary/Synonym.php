<?php


namespace Commune\Chatbot\OOHost\NLU\Dictionary;


use Illuminate\Support\Collection;

class Synonym extends Collection
{
    /**
     * @var string
     */
    protected $id;

    /**
     * Synonym constructor.
     * @param string $id
     * @param mixed $words
     */
    public function __construct(string $id, $words = [])
    {
        $this->id = $id;
        parent::__construct($words);
    }

    public function getId() : string
    {
        return $this->id;
    }


}