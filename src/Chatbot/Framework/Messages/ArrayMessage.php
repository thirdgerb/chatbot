<?php


namespace Commune\Chatbot\Framework\Messages;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\Tags\Transformed;

class ArrayMessage extends AbsMessage implements \ArrayAccess, Transformed
{
    /**
     * @var Message
     */
    protected $origin;

    /**
     * @var array
     */
    protected $data;

    /**
     * ArrayMessage constructor.
     * @param Message $origin
     * @param array $array
     */
    public function __construct(Message $origin, array $array)
    {
        $this->origin = $origin;
        $this->data = $array;
        parent::__construct();
    }


    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    public function getText(): string
    {
        return $this->getOriginMessage()->getText();
    }

    public function toMessageData(): array
    {
        return $this->data;
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function getOriginMessage(): Message
    {
        return $this->origin;
    }


}