<?php

namespace Commune\Chatbot\OOHost\Context\Registrar;

use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Framework\Exceptions\ChatbotLogicException;
use Commune\Chatbot\OOHost\Context\ContextRegistrar;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\PlaceholderDefinition;
use Commune\Chatbot\OOHost\Context\SelfRegister;
use Commune\Support\Utils\StringUtils;
use Commune\Chatbot\OOHost\Context\Intent\PlaceHolderIntentDef;

abstract class AbsContextRegistrar implements ContextRegistrar
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
     * @var Application
     */
    protected $chatApp;

    /**
     * ContextRegistrarImpl constructor.
     * @param Application $chatApp
     */
    public function __construct(Application $chatApp)
    {
        $this->chatApp = $chatApp;
    }


    public function getRegistrarId(): string
    {
        return static::class;
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

        $id = $def->getName();
        if (! static::validateDefName($id)) {
            throw new ChatbotLogicException(
                __METHOD__
                . " context name  $id is invalid"
            );
        }

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

    /*--------- self methods ----------*/

    public function hasDef(string $contextName) : bool
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
        return array_key_exists($id, $this->definitionsByName);
    }

    public function getDef(string $contextName) : ? Definition
    {
        $id = $this->filterFetchId($contextName);
        return $this->definitionsByName[$id] ?? null;
    }

    public function countDef() : int
    {
        return count($this->definitionsByName);
    }

    public function eachDef() : \Generator
    {
        foreach ($this->definitionsByName as $def) {
            yield $def;
        }
    }

    public function getDefNamesByTag(string ...$tags): array
    {
        $names = [];
        foreach ($tags as $tag) {
            if (isset($this->tagToName[$tag])) {
                $names = array_merge($names, $this->tagToName[$tag]);
            }
        }
        return array_unique($names);
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

    public function getPlaceholderDefNames(): array
    {
        return array_keys($this->placeholders);
    }


    public function getDefNamesByDomain(string $domain = '') : array
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

    /*----------- 公共方法 ----------*/

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

    final public static function validateDefName(string $contextName): bool
    {
        return StringUtils::validateDefName($contextName);
    }


}