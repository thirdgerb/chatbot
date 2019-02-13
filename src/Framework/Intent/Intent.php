<?php

/**
 * Class IntentData
 * @package Commune\Chatbot\Framework\Intent
 */

namespace Commune\Chatbot\Framework\Intent;


use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Exceptions\ChatbotException;
use Commune\Chatbot\Framework\Message\Message;
use Commune\Chatbot\Framework\Support\ArrayAbleToJson;
use Illuminate\Support\Arr;

class Intent implements \ArrayAccess, \JsonSerializable
{
    use ArrayAbleToJson;

    /**
     * @var array | \ArrayAccess
     */
    protected $entities = [];

    /**
     * @var Message
     */
    protected $message;

    /**
     * @var string
     */
    protected $id;


    public function __construct(
        Message $message,
        $entities = [],
        string $id = ''
    )
    {
        $this->message = $message;
        if (!Arr::accessible($entities)) {
            //todo
            throw new ChatbotException();
        }
        $this->entities = $entities;
        $this->id = $id;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getEntities()
    {
        if (is_object($this->entities) && method_exists($this->entities, 'toArray')) {
            return $this->entities->toArray();
        }

        return $this->entities;
    }

    public function toContext() : ? Context
    {
        return $this->entities instanceof Context ? $this->entities : null;
    }

    public function toArray() : array
    {
        return [
            'id' => $this->getId(),
            'message' => $this->getMessage()->toArray(),
            'entities' => $this->getEntities(),
        ];
    }

    public function get(string $entityName) : ? string
    {
        return $this->entities[$entityName] ?? null;
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
        throw new \BadMethodCallException();
    }

    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException();
    }

}