<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Struct\Reflection;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IStructReflection implements StructReflection
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $strict;

    /**
     * @var string[]
     */
    protected $relations = [];

    /**
     * @var StructProperty[]
     */
    protected $properties = [];

    /**
     * IStructReflection constructor.
     * @param string $name
     * @param bool $strict
     * @param StructProperty[] $properties
     */
    public function __construct(
        string $name,
        bool $strict,
        array $properties
    )
    {
        $this->name = $name;
        $this->strict = $strict;
        foreach ($properties as $property) {
            $this->defineProperty($property);
        }
    }


    public function getStructName(): string
    {
        return $this->name;
    }

    public function isStrict(): bool
    {
        return $this->strict;
    }

    public function getDefinedPropertyMap(): array
    {
        return $this->properties;
    }

    public function isPropertyDefined(string $name) : bool
    {
        return isset($this->properties[$name]);
    }

    public function getProperty(string $name): ? StructProperty
    {
        return $this->properties[$name] ?? null;
    }

    public function defineProperty(StructProperty $property): void
    {
        $name = $property->getName();
        $this->properties[$name] = $property;
        if ($property->isRelation()) {
            $this->relations[$name] = true;
        }
    }

    public function getRelationNames(): array
    {
        return array_keys($this->relations);
    }

    public function validate(array $data): ? string
    {
        foreach ($data as $name => $val) {
            if ($this->isPropertyDefined($name)) {
                $error = $this->getProperty($name)->validateValue($val);
            }

            if (isset($error)) {
                return $error;
            }
        }

        return null;
    }


}