<?php


namespace Commune\Chatbot\OOHost\NLU\Dictionary;


use Illuminate\Support\Collection;

class Entity extends Collection
{
    /**
     * @var string
     */
    protected $id;

    public function __construct(string $id, $items = [])
    {
        $this->id = $id;
        parent::__construct($items);
    }

    public function getId() : string
    {
        return $this->id;
    }


}