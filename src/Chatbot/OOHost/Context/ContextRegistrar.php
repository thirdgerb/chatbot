<?php

namespace Commune\Chatbot\OOHost\Context;

use Commune\Chatbot\Framework\Utils\StringUtils;
use Commune\Chatbot\OOHost\Context\Intent\PlaceHolderIntentDef;
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
     * 仍然是placeholder 的context
     * @var string[]
     */
    protected $placeholders = [];


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

        // 占位符逻辑
        if ($def instanceof PlaceHolderIntentDef && isset($this->definitionsByName[$id])) {
            return false;
        }

        // 如果已注册是被占位符注册的. 就强制覆盖.
        if (
            !$def instanceof PlaceHolderIntentDef
            && isset($this->definitionsByName[$id])
            && $this->definitionsByName[$id] instanceof PlaceHolderIntentDef
        ) {
            $force = true;
        }

        // 非强制的时候, 只注册一次.
        if (!$force && isset($this->definitionsByName[$id])) {
            return false;
        }

        $this->definitionsByName[$id] = $def;
        $this->classToName[$def->getClazz()] = $def->getName();

        // 注册tag . placeholder 没 tag 权..
        if (!$def instanceof PlaceholderDefinition) {
            $tags = $def->getTags();
            if (!empty($tags)) {
                foreach ($tags as $tag) {
                    $this->tagToName[$tag][] = $id;
                }
            }
        }

        // 记住哪些是placeHolder, 必要时清除
        if ($def instanceof PlaceholderDefinition) {
            $this->placeholders[$id] = true;
        } else {
            unset($this->placeholders[$id]);
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
        $domain = trim($domain, '.');

        $results = [];
        foreach ($this->definitionsByName as $id => $def) {

            if (strpos($id, $domain) === 0) {
                $results[] = $id;
            }

        }
        return $results;
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

    public function getPlaceholders(): array
    {
        return array_keys($this->placeholders);
    }

    public static function validateName(string $contextName): bool
    {
        $name = StringUtils::namespaceSlashToDot($contextName);
        $secs = explode('.', $name);
        foreach ($secs as $sec) {
            if (! preg_match('/^[a-zA-Z0-9]+$/', $sec)) {
                return false;
            }
        }

        return true;
    }


}