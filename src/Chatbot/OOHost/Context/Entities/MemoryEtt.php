<?php


namespace Commune\Chatbot\OOHost\Context\Entities;

use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Entity;
use Commune\Chatbot\OOHost\Context\Memory\Memory;
use Commune\Chatbot\OOHost\Context\Memory\MemoryRegistrar;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * @property-read string $name
 */
class MemoryEtt implements Entity
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $memoryName;

    /**
     * MemoryEtt constructor.
     * @param string $name
     * @param string $memoryName
     */
    public function __construct(string $name, string $memoryName)
    {
        $this->name = $name;
        $this->memoryName = $memoryName;
    }


    public function set(Context $self, $value): void
    {
        if ($value instanceof Memory && $value->getName() === $this->memoryName) {
            $self->setAttribute($this->name, $value);
        }
    }

    public function get(Context $self)
    {
        return $self->getAttribute($this->name);
    }

    protected function newMemory() : Memory
    {
        $memory = $this->getMemoryDef()
            ->newContext();
        /**
         * @var Memory $memory
         */
        return $memory;
    }

    protected function getMemoryRealName() : string
    {
        return $this->getMemoryDef()->getName();
    }

    protected function getMemoryDef() : Definition
    {
        return MemoryRegistrar::getIns()->get($this->memoryName);
    }

    public function isPrepared(Context $self): bool
    {
        $memory = $self->getAttribute($this->name);
        return isset($memory)
            && $memory instanceof Memory
            && $memory->getName() === $this->getMemoryRealName()
            && $memory->isPrepared();
    }

    public function asStage(Stage $stageRoute): Navigator
    {
        return $stageRoute->dependOn(
            $this->newMemory(),
            function(Context $self, Dialog $dialog, Memory $message){
                $self->setAttribute($this->name, $message);
                return $dialog->next();
            }
        );
    }


    public function __get($name)
    {
        return $this->{$name};
    }

}