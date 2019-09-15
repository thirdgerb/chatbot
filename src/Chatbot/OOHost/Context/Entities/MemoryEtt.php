<?php


namespace Commune\Chatbot\OOHost\Context\Entities;

use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Entity;
use Commune\Chatbot\OOHost\Context\Memory\Memory;
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

    protected $question = '';

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

    protected function newMemory(Context $self) : Memory
    {
        $memory = $this
            ->getMemoryDef($self)
            ->newContext();
        /**
         * @var Memory $memory
         */
        return $memory;
    }


    protected function getMemoryDef(Context $self) : Definition
    {
        return $self->getSession()
            ->memoryRepo
            ->getDef($this->memoryName);
    }


    public function isPrepared(Context $self): bool
    {
        $memory = $self->getAttribute($this->name);

        return isset($memory) // 属性存在
            // 类型正确
            && $memory instanceof Memory
            // 是同一个memory
            && $memory->getName() === $this->getMemoryDef($self)->getName()
            // memory 数据完备
            && $memory->isPrepared();
    }

    public function asStage(Stage $stageRoute): Navigator
    {
        return $stageRoute->dependOn(
            $this->newMemory($stageRoute->self),
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