<?php


namespace Commune\Chatbot\App\Memories;


use Commune\Support\Utils\StringUtils;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Memory\AbsMemory;
use Commune\Chatbot\OOHost\Context\Memory\MemoryDefinition;
use Commune\Chatbot\OOHost\Context\Contracts\RootMemoryRegistrar;
use Commune\Chatbot\OOHost\Context\SelfRegister;
use Commune\Chatbot\OOHost\Session\Scope;
use Commune\Chatbot\OOHost\Session\SessionInstance;
use Commune\Container\ContainerContract;

/**
 * 默认的 memory
 * 用类来快速定义 memory 上下文.
 * 可以飞快地定义出新的 memory
 *
 * 1. 类名就是 memory 的ID.
 * 2. 使用 "@property [type] $name [$description]" 的注解来预定义 Entity.
 * 3. 使用常量 SCOPE_TYPES 来定义作用域.
 */
abstract class MemoryDef extends AbsMemory implements SelfRegister
{
    const DESCRIPTION = 'define description';

    const SCOPE_TYPES = [Scope::SESSION_ID];

    /**
     * 可以唯一new 出来.
     */
    public function __construct()
    {
        parent::__construct($this->init());
    }

    /**
     * 默认的 memory 没有数据
     * @return array
     */
    protected function init() : array
    {
        return [];
    }

    final public function getName(): string
    {
        return static::getContextName();
    }

    final public static function getContextName() : string
    {
        return StringUtils::normalizeContextName(static::class);
    }

    final public function getScopingTypes(): array
    {
        return static::SCOPE_TYPES;
    }

    /**
     * 可以在context 中通过 className::from($this) 的方式快速获取memory实例.
     * @param SessionInstance $instance
     * @return static
     */
    public static function from(SessionInstance $instance) : AbsMemory
    {
        $memory = new static();
        if ($instance->isInstanced()) {
            return $memory->toInstance($instance->getSession());
        }
        return $memory;
    }

    public static function registerSelfDefinition(ContainerContract $app): void
    {
        $repo = $app->get(RootMemoryRegistrar::class);
        $def = static::buildDefinition();
        $repo->registerDef($def, true);
    }

    public static function __depend(Depending $depending): void
    {
        $depending->onAnnotations();
    }

    public function getDef(): Definition
    {
        $repo = $this->getSession()->memoryRepo;
        $name = $this->getName();
        if (!$repo->hasDef($name)) {
            $repo->registerDef(static::buildDefinition());
        }

        return $repo->getDef($name);
    }

    protected static function buildDefinition() : Definition
    {
        return new MemoryDefinition(
            static::getContextName(),
            static::class,
            static::DESCRIPTION,
            static::SCOPE_TYPES
        );
    }


}