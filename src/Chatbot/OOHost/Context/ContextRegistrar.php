<?php


namespace Commune\Chatbot\OOHost\Context;
use Commune\Chatbot\Blueprint\Application;

/**
 * 保存
 */
interface ContextRegistrar
{

    public function getParent() : ? ContextRegistrar;

    public function getChatApp() : Application;

    public function addSubRegistrar(string $name, ContextRegistrar $registrar) : void;

    /**
     * @return ContextRegistrar[]
     */
    public function getSubRegistrars() : array;

    /**
     * @param Definition $def
     * @param bool $force  override existing definition
     * @return bool
     */
    public function registerDef(Definition $def, bool $force = false)  : bool;

    public function hasDef(string $contextName) : bool;

    public function getDef(string $contextName) : ? Definition;

    /**
     * @param string $contextName
     * @return bool
     */
    public static function validateDefName(string $contextName) : bool;

    /**
     * @return \Generator of Definition
     */
    public function eachDef() : \Generator;

    /**
     * 已注册的context 数量.
     * @return int
     */
    public function countDef() : int;

    /**
     * @param string $domain
     * @return string[]
     */
    public function getDefNamesByDomain(string $domain = '') : array;


    /**
     * 按 definition->getTags() 的tag 来反查.
     * @param string[] $tags
     * @return string[]
     */
    public function getDefNamesByTag(string ...$tags) : array;

    /**
     * 仍然使用了 placeholder 的context
     * @return string[]
     */
    public function getPlaceholderDefNames() : array;
}