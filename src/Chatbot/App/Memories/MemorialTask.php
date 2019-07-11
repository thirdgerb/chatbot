<?php


namespace Commune\Chatbot\App\Memories;


use Commune\Chatbot\Framework\Utils\StringUtils;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Memory\AbsMemory;
use Commune\Chatbot\OOHost\Context\Memory\MemoryDefinition;
use Commune\Chatbot\OOHost\Context\SelfRegister;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Session\Scope;
use Commune\Chatbot\OOHost\Session\SessionInstance;

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
        $repo->register($def, true);
    }

    public function getDef(): Definition
    {
        $repo = static::getRegistrar();
        $name = $this->getName();
        if (!$repo->has($name)) {
            static::registerSelfDefinition();
        }

        return $repo->get($name);
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