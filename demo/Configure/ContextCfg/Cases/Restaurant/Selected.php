<?php

/**
 * Class Selected
 * @package Commune\Chatbot\Demo\Configure\ContextCfg\Cases\Restaurant
 */

namespace Commune\Chatbot\Demo\Configure\ContextCfg\Cases\Restaurant;


class Selected
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $num;

    /**
     * @var array
     */
    private $tags = [];

    /**
     * Selected constructor.
     * @param string $name
     * @param int $num
     * @param array $tags
     */
    public function __construct(string $name, int $num, array $tags)
    {
        $this->name = $name;
        $this->num = $num;
        $this->tags = $tags;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getNum(): int
    {
        return $this->num;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    public function toString() : string
    {
        $str = $this->name . ', ' . $this->num .'份';

        if (!empty($this->tags)) {
            $str .= ', 需要' . implode($this->tags, ',');
        }
        return $str;
    }
}