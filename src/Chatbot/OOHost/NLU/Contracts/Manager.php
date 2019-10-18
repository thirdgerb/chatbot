<?php


namespace Commune\Chatbot\OOHost\NLU\Contracts;


use Commune\Support\Option;

interface Manager
{
    public function sync(bool $force = false) : string;

    public function count() : int;

    public function has(string $id) : bool;

    public function get(string $id) : Option;

    public function each() : \Generator;

    public function remove(string $id) : void;

    /**
     * @param string ...$ids
     * @return Option[]
     */
    public function getMap(string ...$ids) : array;

    public function save(Option $option) : string;

}