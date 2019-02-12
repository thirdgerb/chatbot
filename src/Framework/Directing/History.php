<?php

/**
 * Class History
 * @package Commune\Chatbot\Host\Direction
 */

namespace Commune\Chatbot\Framework\Directing;

class History
{
    const MAX_HISTORY = 40;

    /**
     * @var string
     */
    protected $chatId;

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var Location
     */
    protected $current;

    protected $before = [];

    protected $after = [];

    /**
     * @var Location
     */
    protected $root;

    public function __construct(string $chatId, string $sessionId, Location $root)
    {
        $this->chatId = $chatId;
        $this->sessionId = $sessionId;
        $this->root = $root;
        $this->current = $root;
    }

    public function home() : Location
    {
        $this->to($this->root);
        return $this->root;
    }

    public function current() : Location
    {
        return $this->current ?? $this->root;
    }

    public function setCurrent(Location $location)
    {
        $this->current = $location;
    }

    public function prev() : ? Location
    {
        return end($this->before) ? : null;
    }

    public function next() : ? Location
    {
        return reset($this->after) ? : null;
    }

    public function forward() : Location
    {
        if ($location = $this->next()) {
            array_push($this->before, $this->current);
            $this->current = array_shift($this->after);
        }
        return $this->current;
    }

    public function backward() : Location
    {
        if ($location = $this->prev()) {
            array_unshift($this->after, $this->current);
            $this->current = array_pop($this->before);
        }
        return $this->root;
    }

    public function to(Location $location)
    {
        $this->addHistory($this->current);
        $this->after = [];
        $this->current = $location;
    }

    protected function addHistory(Location $location)
    {
        array_push($this->before, $location);
        if (count($this->before) > self::MAX_HISTORY) {
            array_shift($this->before);
        }
    }
    
    public function flush()
    {
        $this->before = [];
        $this->after = [];
        $this->current = $this->root;
    }

    /**
     * @return Location
     */
    public function getCurrent(): Location
    {
        return $this->current;
    }

    /**
     * @return array
     */
    public function getBefore(): array
    {
        return $this->before;
    }

    /**
     * @return array
     */
    public function getAfter(): array
    {
        return $this->after;
    }


}