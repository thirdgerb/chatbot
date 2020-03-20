<?php


namespace Commune\Chatbot\Framework\Conversation;


use Commune\Chatbot\Blueprint\Conversation\User;
use Commune\Support\Arr\ArrayAbleToJson;

class UserImpl implements User
{
    use ArrayAbleToJson;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $origin;

    /**
     * UserImpl constructor.
     * @param string $id
     * @param string $name
     * @param array $origin
     */
    public function __construct(string $id, string $name, array $origin)
    {
        $this->id = $id;
        $this->name = $name;
        $this->origin = $origin;
    }


    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOriginData(): array
    {
        return $this->origin;
    }

    public function offsetExists($offset)
    {
        return isset($this->origin[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->origin[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        $this->origin[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->origin[$offset]);
    }

    public function toArray() : array
    {
        return [
            'name' => $this->getName(),
            'id' => $this->getId(),
            'origin' => $this->getOriginData()
        ];
    }


}