<?php


namespace Commune\Chatbot\OOHost\Context\Entities;


use Commune\Chatbot\Framework\Exceptions\ChatbotLogicException;
use Commune\Support\Utils\StringUtils;
use Commune\Chatbot\OOHost\Command\CommandDefinition;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Entity;
use Commune\Chatbot\OOHost\Context\Memory\Memory;

class DependingBuilder implements Depending
{
    /**
     * @var Definition
     */
    public $definition;

    /**
     * EntityDef constructor.
     * @param Definition $definition
     */
    public function __construct(Definition $definition)
    {
        $this->definition = $definition;
    }

    public function onEntity(Entity $entity) : Depending
    {
        $this->definition->addEntity($entity);
        return $this;
    }

    public function on(string $name, string $question = '', $default = null): Depending
    {
        $this->definition->addEntity(new PropertyEtt($name, $question, $default));
        return $this;
    }

    public function onAnnotations(array $names = null): Depending
    {
        $r = new \ReflectionClass($this->definition->getClazz());
        $doc = $r->getDocComment();
        $properties = StringUtils::fetchPropertyAnnotations($doc);

        foreach ($properties as list($name, $desc)) {
            $this->on($name, $desc);
        }
        return $this;
    }

    public function onCommand(CommandDefinition $cmd): Depending
    {
        foreach ($cmd->getArguments() as $argument) {
            $this->on(
                $argument->getName(),
                $argument->getDescription(),
                $argument->getDefault()
            );
        }

        foreach ($cmd->getOptions() as $option) {
            $this->on(
                $option->getName(),
                $option->getDescription(),
                $option->getDefault()
            );
        }

        return $this;
    }

    public function onSignature(string $signature): Depending
    {
        return $this->onCommand(CommandDefinition::makeBySignature($signature));
    }


    public function onContext(string $name, string $contextName): Depending
    {
        $this->definition->addEntity(new ContextEtt($name, $contextName));
        return $this;
    }


    protected function memoryNeverDependOnMemory() : void
    {
        $clazz = $this->definition->getClazz();
        if (is_a($clazz, Memory::class, TRUE)) {
            throw new ChatbotLogicException(
                'memory context '. $this->definition->getName()
                . ' should not register entity from another memory... '
            );
        }
    }

    public function onMemory(string $name, string $memoryName): Depending
    {
        $this->memoryNeverDependOnMemory();
        $this->definition->addEntity($e = new MemoryEtt($name, $memoryName));
        return $this;
    }

    public function onMemoryVal(string $name, string $memoryName, string $memoryKey, string $question = ''): Depending
    {
        $this->memoryNeverDependOnMemory();

        $this->definition->addEntity(
            new PropertyEtt($name, $question, null, $memoryName, $memoryKey)
        );
        return $this;
    }


}