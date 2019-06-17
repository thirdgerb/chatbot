<?php


namespace Commune\Chatbot\App\Memories;


use Commune\Chatbot\Framework\Utils\StringUtils;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Memory\AbsMemory;
use Commune\Chatbot\OOHost\Context\Memory\MemoryDefinition;
use Commune\Chatbot\OOHost\Context\SelfRegister;
use Commune\Chatbot\OOHost\Session\Scope;
use Commune\Chatbot\OOHost\Session\SessionInstance;

/**
 * 默认的 memory
 * 用类 来定义 memory
 *
 * 1. 类名就是 memory 的ID.
 * 2. 使用 "@property [type] $name [$description]" 的注解来预定义参数.
 * 3. 使用常量 SCOPE_TYPES 来定义作用域.
 */
class MemoryDef extends AbsMemory implements SelfRegister
{
    const DESCRIPTION = 'define description';

    const SCOPE_TYPES = [Scope::SESSION_ID];

    /**
     * 可以唯一new 出来.
     */
    final public function __construct()
    {
        parent::__construct([]);
    }

    final public function getName(): string
    {
        return static::getContextName();
    }

    final public static function getContextName() : string
    {
        return StringUtils::namespaceSlashToDot(static::class);
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

    public static function registerSelfDefinition(): void
    {
        $repo = static::getRegistrar();
        $def = static::buildDefinition();
        $repo->register($def);
    }

    public static function __depend(Depending $depending): void
    {
        $depending->onAnnotations();
    }

    public function getDef(): Definition
    {
        $repo = static::getRegistrar();
        $name = $this->getName();
        if ($repo->has($name)) {
            return $repo->get($name);
        }

        $def = static::buildDefinition();
        $repo->register($def);
        return $def;
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