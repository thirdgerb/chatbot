<?php

/**
 * Class User
 * @package Commune\Chatbot\Charactor
 */

namespace Commune\Chatbot\Framework\Character;


class User
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $originId;

    /**
     * @var Platform
     */
    private $platform;

    /**
     * @var array
     */
    private $origin;

    /**
     * User constructor.
     * @param string $id
     * @param string $name
     * @param string $originId
     * @param Platform $platform
     * @param array $origin
     */
    public function __construct(
        string $id,
        string $name,
        string $originId,
        Platform $platform,
        array $origin
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->originId = $originId;
        $this->platform = $platform;
        $this->origin = $origin;
    }


    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getOriginId(): string
    {
        return $this->originId;
    }


    /**
     * @return array
     */
    public function getOrigin(): array
    {
        return $this->origin;
    }

    /**
     * @return Platform
     */
    public function getPlatform(): Platform
    {
        return $this->platform;
    }

    public function toArray() : array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'originId' => $this->originId,
            'platform' => $this->platform->toArray(),
            'origin' => $this->origin,
        ];
    }


    public function toJson() : string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

}