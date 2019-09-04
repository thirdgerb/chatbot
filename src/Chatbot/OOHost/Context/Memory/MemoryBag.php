<?php


namespace Commune\Chatbot\OOHost\Context\Memory;


use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Depending;

/**
 * memory 的占位符
 * 单纯用作 session 获取memory的办法.
 * 没有上下文能力.
 */
class MemoryBag extends AbsMemory
{
    /**
     * @var string
     */
    protected $name;

    public function __construct(string $name)
    {
        $this->name = $name;
        parent::__construct([]);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDesc(): string
    {
        return $this->getDef()->getDesc();
    }


    public static function __depend(Depending $depending): void
    {
    }

    public function getScopingTypes(): array
    {
        return $this->getDef()->getScopeTypes();
    }

    /**
     * @return MemoryDefinition
     */
    public function getDef(): Definition
    {
        $registrar = $this->getSession()->memoryRepo;
        $name = $this->getName();

        if ($registrar->hasDef($name)) {
            return $registrar->getDef($name);
        }

        throw new ConfigureException(
            static::class
            . ' memory placeholder ' . $name
            . ' is not predefined, only available after be register to repo'
        );
    }

    public function __sleep(): array
    {
        $fields = parent::__sleep();
        $fields[] = 'name';
        return $fields;
    }
}