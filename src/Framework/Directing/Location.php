<?php

/**
 * Class Location
 * @package Commune\Chatbot\Host\Direction
 */

namespace Commune\Chatbot\Framework\Directing;

class Location
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
    private $callback;


    public function __construct(string $contextName, array $props = [], string $id = null)
    {
        $this->contextName = $contextName;
        $this->props = $props;
        $this->contextId = $id;
    }

    public function through(string $contextName, array $props, string $callback = null) : Location
    {
        $guest = new Location($contextName, $props);
        $guest->setCallback($callback);

        $guest->setIntended($this);
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

    public function setIntended(Location $intended)
    {
        //需要避免循环, 死循环.
        $this->intended = $intended;
    }

    /**
     * @return string | null
     */
    public function getCallback(): ? string
    {
        return $this->callback;
    }

    /**
     * @param string $callback
     */
    public function setCallback(string $callback = null): void
    {
        $this->callback = $callback;
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

    public function toString() : string
    {
        return 'location:{name='.$this->contextName.';id='.$this->contextId.';}';
    }

    public function __toString()
    {
        return $this->toString();
    }
}