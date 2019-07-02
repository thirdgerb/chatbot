<?php

namespace Commune\Chatbot\OOHost\Context;

use Commune\Chatbot\Framework\Utils\StringUtils;
use Illuminate\Support\Arr;

class ContextRegistrar implements Registrar
{
    const DEF_CLAZZ = Definition::class;

    /**
     * @var Definition[]
     */
    protected $definitionsByName = [];

    /**
     * @var string[]
     */
    protected $classToName = [];

    /**
     * @var string[][]
     */
    protected $tagToName = [];

    /**
     * @var Registrar[]
     */
    private static $instances;

    /**
     * @var array
     */
    protected $domainTrees = [];


    final private function __construct()
    {
    }

    /**
     * @return static
     */
    final public static function getIns() : Registrar
    {
        $name = static::class;
        return self::$instances[$name]
            ?? self::$instances[$name] = new static();
    }


    public function register(Definition $def, bool $force = false) : bool
    {
        if (!is_a($def, static::DEF_CLAZZ, TRUE)) {
            throw new \InvalidArgumentException(
                __METHOD__
                . ' only accept '. static::DEF_CLAZZ
                . ', ' . get_class($def)
                . ' given'
            );
        }

        $id = $this->filterContextName($def->getName());

        // 非强制的时候, 只注册一次.
        if (!$force && isset($this->definitionsByName[$id])) {
            return false;
        }

        $this->definitionsByName[$id] = $def;
        $this->classToName[$def->getClazz()] = $def->getName();
        Arr::set($this->domainTrees, $id, $id);


        // 注册tag
        $tags = $def->getTags();
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $this->tagToName[$tag][] = $id;
            }
        }

        return true;
    }

    protected function filterContextName(string $name ) : string
    {
        return StringUtils::namespaceSlashToDot($name);
    }

    public function has(string $contextName) : bool
    {
        // 如果传入了一个类名, 可能要临时注册.
        if (class_exists($contextName)) {
            // 先检查是否注册过了.
            if (isset($this->classToName[$contextName])) {
                return true;
            }

            // 再检查是否能自动注册.
            if (is_a($contextName, SelfRegister::class, TRUE)) {
                $method = [$contextName, SelfRegister::REGISTER_METHOD];
                call_user_func($method);
                return isset($this->classToName[$contextName]);
            }
        }

        // 最后检查是不是一个合法的ID.
        $id = $this->filterFetchId($contextName);
        return isset($this->definitionsByName[$id]);
    }

    public function get(string $contextName) : ? Definition
    {
        $id = $this->filterFetchId($contextName);
        return $this->definitionsByName[$id] ?? null;
    }

    protected function filterFetchId(string $contextName) : string
    {
        if (
            class_exists($contextName)
            && isset($this->classToName[$contextName])
        ) {
            $id = $this->classToName[$contextName];
        } else {
            $id = $this->filterContextName($contextName);
        }

        return $id;
    }

    /**
     * @return \Generator
     */
    public function each() : \Generator
    {
        foreach ($this->definitionsByName as $def) {
            yield $def;
        }
    }


    public function getNamesByDomain(string $domain) : array
    {
        if (empty($domain)) {
            return array_keys($this->definitionsByName);
        }

        $domain = $this->filterContextName($domain);
        $element = Arr::get($this->domainTrees, $domain);
        if (!isset($element)) {
            return [];
        }

        if (is_array($element)) {
            return Arr::flatten($element);
        }

        return [$element];
    }

    public function count(): int
    {
        return count($this->definitionsByName);
    }

    public function getNamesByTag(string ...$tags): array
    {
        $names = [];
        foreach ($tags as $tag) {
            if (isset($this->tagToName[$tag])) {
                $names = array_merge($names, $this->tagToName[$tag]);
            }
        }
        return array_unique($names);
    }


}