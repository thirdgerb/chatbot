<?php

namespace Commune\Chatbot\OOHost\Context;

use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Support\Utils\StringUtils;
use Commune\Chatbot\OOHost\Context\Intent\PlaceHolderIntentDef;

class ContextRegistrarImpl implements ContextRegistrar
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
     * 仍然是placeholder 的context
     * @var string[]
     */
    protected $placeholders = [];

    /**
     * @var ContextRegistrar[]
     */
    protected $subRegistrars = [];


    /**
     * @var ContextRegistrar|null
     */
    protected $parent;

    /**
     * @var Application;
     */
    protected $chatApp;

    public function __construct(Application $app, ContextRegistrar $parent = null)
    {
        $this->chatApp = $app;
        if (isset($parent)) {
            $this->parent = $parent;
            $this->registerSelfToParent($parent);
        }
    }

    protected function registerSelfToParent(ContextRegistrar $parent) : void
    {
        $id = $this->getRegistrarId();
        if ($parent->hasSubRegistrar($id)) {
            $parentId = $parent->getRegistrarId();
            throw new ConfigureException(
                "parent context registrar $parentId already has sub context registrar named $id"
            );
        }
        $parent->addSubRegistrar($id, $this);
    }

    public function getRegistrarId() : string
    {
        return static::class;
    }

    public function getParent(): ? ContextRegistrar
    {
        return $this->parent;
    }

    public function getChatApp(): Application
    {
        return $this->chatApp;
    }

    public function hasSubRegistrar(string $registrarName): bool
    {
        if ($registrarName == $this->getRegistrarId()) {
            return true;
        }

        if (array_key_exists($registrarName, $this->subRegistrars)) {
            return true;
        }

        if (isset($this->parent)) {
            return $this->parent->hasSubRegistrar($registrarName);
        }

        return false;
    }


    public function addSubRegistrar(string $name, ContextRegistrar $registrar): void
    {
        if (!$registrar instanceof self) {
            throw new ConfigureException(
                'sub registrar must extends parent registrar, '
                .get_class($registrar)
                .' given for '
                . static::class
            );
        }

        $this->subRegistrars[$name] = $registrar;
    }

    public function getSubRegistrars($recursive = true): array
    {
        $subs = $this->subRegistrars;

        $registrars = $subs;
        if ($recursive) {
            foreach ($subs as $sub) {
                array_merge($registrars, $sub->getSubRegistrars());
            }
        }
        return $registrars;
    }

    public function registerDef(Definition $def, bool $force = false) : bool
    {
        if (!is_a($def, static::DEF_CLAZZ, TRUE)) {
            throw new \InvalidArgumentException(
                __METHOD__
                . ' only accept '. static::DEF_CLAZZ
                . ', ' . get_class($def)
                . ' given'
            );
        }

        $id = $this->normalizeContextName($def->getName());

        // 占位符逻辑 占位符低优先, 不能覆盖.
        if (
            $def instanceof PlaceHolderIntentDef
            && isset($this->definitionsByName[$id])
        ) {
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

        // 注册def
        $this->definitionsByName[$id] = $def;
        // 注册类名. 理论上可能重复.
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

    protected function normalizeContextName(string $name ) : string
    {
        return StringUtils::normalizeContextName($name);
    }

    /*--------- has ----------*/

    public function hasDef(string $contextName) : bool
    {
        $has = $this->selfHasDef($contextName);

        if ($has) {
            return $has;
        }

        // 递归查找.
        foreach ($this->getSubRegistrars() as $registrar) {
            if ($registrar->hasDef($contextName)) {
                return true;
            }
        }

        return false;
    }

    protected function selfHasDef(string $contextName) : bool
    {
        // 如果传入了一个类名, 可能要临时注册.
        if (class_exists($contextName)) {
            // 先检查是否注册过了.
            if (isset($this->classToName[$contextName])) {
                return true;
            }

            // 再检查是否能自动注册.
            if (is_a($contextName, SelfRegister::class, TRUE)) {//
                $method = [$contextName, SelfRegister::REGISTER_METHOD];
                call_user_func($method, $this->chatApp->getProcessContainer());
                return isset($this->classToName[$contextName]);
            }
        }

        // 最后检查是不是一个合法的ID.
        $id = $this->filterFetchId($contextName);
        return $this->hasDefById($id);
    }

    /**
     * 可以重写.
     * @param string $id
     * @return bool
     */
    public function hasDefById(string $id) : bool
    {
        return isset($this->definitionsByName[$id]);
    }


    public function getDef(string $contextName) : ? Definition
    {
        $def = $this->getSelfDef($contextName);
        if (isset($def)) {
            return $def;
        }

        foreach ($this->getSubRegistrars() as $registrar) {
            $def = $registrar->getDef($contextName);
            if (isset($def)) {
                return $def;
            }
        }

        return null;
    }

    public function getSelfDef(string $contextName) : ? Definition
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
            $id = $this->normalizeContextName($contextName);
        }

        return $id;
    }

    /**
     * @return \Generator
     */
    public function eachDef() : \Generator
    {
        foreach ($this->definitionsByName as $def) {
            yield $def;
        }

        foreach ($this->getSubRegistrars() as $registrar) {
            foreach ($registrar->eachDef() as $def) {
                yield $def;
            }
        }
    }

    public function getDefNamesByDomain(string $domain = '') : array
    {
        $names = $this->getSelfDefNamesByDomain($domain);
        foreach ($this->subRegistrars as $registrar) {
            $result = $registrar->getDefNamesByDomain($domain);
            $names = array_merge($names, $result);
        }

        return array_unique($names);
    }

    public function getSelfDefNamesByDomain(string $domain) : array
    {
        if (empty($domain)) {
            return array_keys($this->definitionsByName);
        }

        $domain = $this->normalizeContextName($domain);
        $domain = trim($domain, '.');

        $results = [];
        foreach ($this->definitionsByName as $id => $def) {

            if (strpos($id, $domain) === 0) {
                $results[] = $id;
            }

        }
        return $results;
    }

    public function countDef(): int
    {
        $num = $this->countSelfDef();
        foreach ($this->subRegistrars as $registrar) {
            $num += $registrar->countDef();
        }
        return $num;
    }

    public function countSelfDef() : int
    {
        return count($this->definitionsByName);
    }

    public function getDefNamesByTag(string ...$tags): array
    {
        $names = $this->getSelfDefNamesByTag($tags);
        foreach ($this->subRegistrars as $registrar) {
            $names = array_merge($names, $registrar->getDefNamesByTag($tags));
        }
        return array_unique($names);
    }


    public function getSelfDefNamesByTag(string ...$tags): array
    {
        $names = [];
        foreach ($tags as $tag) {
            if (isset($this->tagToName[$tag])) {
                $names = array_merge($names, $this->tagToName[$tag]);
            }
        }
        return array_unique($names);
    }

    public function getPlaceholderDefNames(): array
    {
        $names = $this->getSelfPlaceholderDefNames();
        foreach ($this->subRegistrars as $registrar) {
            $names = array_merge($names, $registrar->getPlaceholderDefNames());
        }
        return $names;
    }


    public function getSelfPlaceholderDefNames(): array
    {
        return array_keys($this->placeholders);
    }

    final public static function validateDefName(string $contextName): bool
    {
        $name = StringUtils::normalizeContextName($contextName);
        $secs = explode('.', $name);
        foreach ($secs as $sec) {
            if (! preg_match('/^[a-z0-9_]+$/', $sec)) {
                return false;
            }
        }

        return true;
    }


}