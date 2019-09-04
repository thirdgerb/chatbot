<?php


namespace Commune\Chatbot\OOHost\NLU\Contracts;


use Commune\Chatbot\OOHost\NLU\Dictionary\Entity;
use Commune\Chatbot\OOHost\NLU\Dictionary\Synonym;

/**
 * 实体和同义词词典.
 */
interface Dictionary
{
    /*-------- entities -------*/

    public function hasEntity(string $name): bool;

    public function getEntity(string $name): ? Entity;

    public function setEntity(Entity $entity): void;

    public function fetchEntities(int $limit, int $offset = 0): array;


    /*-------- synonym -------*/

    public function hasSynonym(string $name) : bool;

    public function getSynonym(string $name) : ? Synonym;

    public function setSynonym(string $synonym) : void;

    public function fetchSynonym(int $limit, int $offset = 0) : array;

}