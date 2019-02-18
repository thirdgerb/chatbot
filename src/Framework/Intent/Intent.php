<?php

/**
 * Class IntentInterface
 * @package Commune\Chatbot\Framework\Intent
 */

namespace Commune\Chatbot\Framework\Intent;


use Commune\Chatbot\Framework\Message\Message;
use Commune\Chatbot\Framework\Support\ChatbotUtils;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class Intent
 * @see \Symfony\Component\Console\Input
 * @package Commune\Chatbot\Framework\Intent
 */
abstract class Intent implements \ArrayAccess, Arrayable, \JsonSerializable, Jsonable
{

    /**
     * @var IntentDefinition
     */
    protected $definition;

    /**
     * @var Collection
     */
    protected $entities;

    /**
     * @var Message
     */
    protected $message;

    /**
     * id 标记数据的来源.
     * 来源:
     * - Context  id 是 Context::class
     * - MsgCmdIntent id 是 commandName
     * - IntentFactory id 是IntentCfg 或者 commandName, 是命令名
     * - IntentCfg id 是 intentCfg::class
     * - Conversation 是 Conversation::class
     *
     * @var string
     */
    protected $id;

    protected $parsed;

    protected $tokens = [];
    /**
     * @var MessageBag
     */
    protected $errors;

    public function __construct(
        string $id,
        Message $message,
        array $entities = []
    )
    {
        // set default value
        $this->id = empty($id) ? self::class : $id;
        $this->message = $message;
        $this->entities = new Collection($entities);
        $this->errors = new MessageBag();
        $this->definition = new IntentDefinition([]);
    }

    abstract protected function parse(array $tokens);

    public function bind(IntentDefinition $definition)
    {
        $this->definition = $definition;
        $this->entities = new Collection();
        $this->errors = new MessageBag();

        $this->parse($this->tokens);
    }

    public function dependingArguments() : array
    {
        $result = [];

        $arguments = $this->definition->getArguments();
        if (!isset($arguments)) {
            return $result;
        }

        foreach ($arguments as $argument) {
            /**
             * @var InputArgument $argument
             */
            if (!$this->has($argument->getName())) {
                $result[] = $argument;
            }
        }
        return $result;
    }

    public function getErrors() : array
    {
        return $this->errors->toArray();
    }

    public function getId() : string
    {
        return $this->id;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }

    public function get(string $entityName, $default = null)
    {
        return $this->entities->get($entityName, $default);
    }

    public function has(string $name) : bool
    {
        return $this->entities->has($name);
    }

    public function offsetExists($offset)
    {
        $val = $this->get($offset);
        return isset($val);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('intent can not set value');
        //$this->entities[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('intent can not unset value');
        //unset($this->entities[$offset]);
    }

    public function getEntities() : array
    {
        return $this->entities->toArray();
    }

    public function getArgument(string $name)
    {
        if ($this->definition->hasArgument($name)) {
            return $this->get($name);
        }
        return null;
    }

    public function getOption(string $name)
    {
        if ($this->definition->hasOption($name)) {
            return $this->get("--$name");
        }
        return null;
    }

    public function getArguments() : array
    {
        $arguments = $this->definition->getArguments();
        if (!isset($arguments)) {
            return [];
        }

        $result = [];
        foreach ($arguments as $argument) {
            /**
             * @var InputArgument $argument
             */
            $name = $argument->getName();
            $result[$name] = $this->get($name);
        }
        return $result;
    }

    public function getOptions() : array
    {
        $options = $this->definition->getOptions();
        if (!isset($options)) {
            return [];
        }

        $result = [];
        foreach ($options as $option) {
            /**
             * @var InputOption $option
             */
            $name = '--'.$option->getName();
            $result[$name] = $this->get($name);
        }
        return $result;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'message' => $this->getMessage()->toArray(),
            'entities' => $this->getEntities()
        ];
    }

    public function toJson($option = ChatbotUtils::JSON_OPTION) : string
    {
        return json_encode($this->toArray(), $option);
    }

    public function jsonSerialize()
    {
        return $this->toJson();
    }

    public function __toString()
    {
        return $this->toJson();
    }


    public function __sleep()
    {
        return ['id', 'message', 'tokens'];
    }


    public function __wakeup()
    {
        $this->entities = new Collection($this->tokens);
        $this->errors = new MessageBag();
        $this->definition = new IntentDefinition();
    }
}