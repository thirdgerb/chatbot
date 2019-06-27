<?php


namespace Commune\Chatbot\OOHost\Context;



interface Registrar
{
    public function register(Definition $def, bool $force = false)  : bool;

    public function has(string $contextName) : bool;

    public function get(string $contextName) : ? Definition;

    public function each() : \Generator;

    /**
     * 已注册的context 数量.
     * @return int
     */
    public function count() : int;

    public function getNamesByDomain(string $domain) : array;


}