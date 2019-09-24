<?php


namespace Commune\Chatbot\App\Memories;


use Commune\Chatbot\Framework\Utils\StringUtils;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Memory\AbsMemory;
use Commune\Chatbot\OOHost\Context\Memory\MemoryDefinition;
use Commune\Chatbot\OOHost\Context\Memory\MemoryRegistrar;
use Commune\Chatbot\OOHost\Context\SelfRegister;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Session\Scope;
use Commune\Chatbot\OOHost\Session\SessionInstance;
use Commune\Container\ContainerContract;

/**
 * 拥有记忆功能的 context.
 *
 */
abstract class MemorialTask extends AbsMemory implements SelfRegister
{
    const DESCRIPTION = 'define description';

    const SCOPE_TYPES = [Scope::SESSION_ID];

    /**
     * 可以唯一new 出来.
     */
    final public function __construct()
    {
        parent::__construct($this->init());
    }

    /**
     * 数据初始化
     * initialize context properties
     *
     * @return array
     */
    abstract protected function init() : array;

    abstract protected function doStart(Stage $stage) : Navigator;

    public function __onStart(Stage $stage): Navigator
    {
        return $this->doStart($stage);
    }

    final public function getName(): string
    {
        return static::getContextName();
    }

    public static function getContextName() : string
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

    public static function registerSelfDefinition(ContainerContract $processContainer): void
    {
        $repo = $processContainer->get(MemoryRegistrar::class);
        $def = static::buildDefinition();
        $repo->registerDef($def, true);
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