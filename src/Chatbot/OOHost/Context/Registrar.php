<?php


namespace Commune\Chatbot\OOHost\Context;



interface Registrar
{
    public function register(Definition $def, bool $force = false)  : bool;

    public function has(string $contextName) : bool;

    public function get(string $contextName) : ? Definition;

    public function each() : \Generator;

    public function getNamesByDomain(string $domain) : array;


}