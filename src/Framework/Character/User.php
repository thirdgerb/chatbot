<?php

/**
 * Class User
 * @package Commune\Chatbot\Charactor
 */

namespace Commune\Chatbot\Framework\Character;


use Commune\Chatbot\Framework\Support\ArrayAbleToJson;
use Illuminate\Contracts\Support\Arrayable;

class User implements Arrayable, \JsonSerializable
{
    use ArrayAbleToJson;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $avatar;
    
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $originId;

    /**
     * @var Platform
     */
    protected $platform;

    /**
     * @var array
     */
    protected $origin;

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
        array $origin = [],
        string $avatar = ''
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->originId = $originId;
        $this->platform = $platform;
        $this->origin = $origin;
        $this->avatar = $avatar;
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
    
    public function getAvatar() : string
    {
        return $this->avatar;
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
            'originId' => $this->originId,
            'name' => $this->name,
            'avatar' => $this->avatar,
            'platform' => $this->platform->toArray(),
            'origin' => $this->origin,
        ];
    }

}