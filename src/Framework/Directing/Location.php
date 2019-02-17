<?php

/**
 * Class Location
 * @package Commune\Chatbot\Host\Direction
 */

namespace Commune\Chatbot\Framework\Directing;

use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Support\ChatbotUtils;
use Illuminate\Contracts\Support\Arrayable;

class Location implements Arrayable, \JsonSerializable
{
    /**
     * @var string
     */
    private $contextName;

    /**
     * @var string | null
     */
    private $contextId;

    /**
     * @var array
     */
    private $props = [];

    /**
     * @var Location
     */
    private $intended;

    /**
     * @var string
     */
    private $intentId;


    public function __construct(string $contextName, array $props = [], string $id = null)
    {
        $this->contextName = $contextName;
        $this->props = $props;
        $this->contextId = $id;
    }

    public function through(string $contextName, array $props, string $callback = null): Location
    {
        $guest = new Location($contextName, $props);
        $guest->setCallbackIntentId($callback);

        $guest->pushIntended($this);
        return $guest;
    }

    /**
     * @return string
     */
    public function getContextName(): string
    {
        return $this->contextName;
    }

    /**
     * @return array
     */
    public function getProps(): array
    {
        return $this->props;
    }

    /**
     * @return string | null
     */
    public function getContextId(): ? string
    {
        return $this->contextId;
    }

    /*------- intend -------*/

    public function equals(Location $location): bool
    {
        return $this->contextName == $location->getContextName()
            && $this->contextId == $location->getContextId();
    }

    public function pushIntended(Location $intended)
    {
        if ($this->equals($intended)) {
            //
            throw new ConfigureException();
        }
        $this->intended = $intended;
    }

    /**
     * @return string | null
     */
    public function getCallbackIntentId(): ? string
    {
        return $this->intentId;
    }

    /**
     * @param string $callback
     */
    public function setCallbackIntentId(string $callback = null): void
    {
        $this->intentId = $callback;
    }

    /**
     * @param null|string $contextId
     */
    public function setContextId(?string $contextId): void
    {
        $this->contextId = $contextId;
    }


    /**
     * @return Location
     */
    public function getIntended(): ? Location
    {
        return $this->intended;
    }

    public function toArray() : array
    {
        $intended = $this->getIntended();
        return [
            'name' => $this->getContextName(),
            'id' => $this->getContextId(),
            'props' => $this->getProps(),
            'callback' => $this->getCallbackIntentId(),
            'intended' => isset($intended) ? $intended->toArray() : null
        ];
    }

    public function toJson(int $option = ChatbotUtils::JSON_OPTION) : string
    {
        return json_encode($this->toArray(), $option);
    }

    public function jsonSerialize()
    {
        return $this->toJson();
    }

    public function toString(): string
    {
        return $this->toJson();
    }

    public function __toString()
    {
        return $this->toString();
    }
}
