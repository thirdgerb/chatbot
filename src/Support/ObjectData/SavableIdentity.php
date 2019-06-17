<?php


namespace Commune\Support\ObjectData;

use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * Class SavableIdentity
 * @package Commune\Support\Savable
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $id
 * @property-read string $type
 */
class SavableIdentity implements ArrayAndJsonAble
{
    use ArrayAbleToJson;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    public function __construct(
        string $id,
        string $type
    )
    {
        $this->id = $id;
        $this->type = $type;
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
    public function getType(): string
    {
        return $this->type;
    }


    public function __get($name)
    {
        return $this->{$name};
    }


    public function toArray() : array
    {
        return [
            'id' => $this->id,
            'type' => $this->type
        ];
    }
}