<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Parameter;

use Commune\Support\Parameter\Param;
use Commune\Support\Parameter\ParamTypeHints;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IParam implements Param
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $typeHints = [];

    /**
     * @var mixed
     */
    protected $default;

    /**
     * @var bool
     */
    protected $nullable;

    public function __construct(string $name, array $typeHints, $default)
    {
        $this->name = $name;
        $this->default = $default;
        $this->initTypeHints($typeHints);
    }

    protected function initTypeHints(array $typeHints) : void
    {
        if (empty($typeHints)) {
            $this->nullable = true;
            return;
        }

        foreach ($typeHints as $typeHint) {
            if ($typeHint === 'null' || $typeHint === 'mixed') {
                $this->nullable = true;
            }

            $this->typeHints[] = $typeHint;
        }
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function getTypeHints(): array
    {
        return $this->typeHints;
    }

    public function parse($value, string $type = null)
    {
        if ($type === 'mixed') {
            return $value;
        }

        $type = $type ?? $this->getValidType($value);

        if (isset($type)) {
            return ParamTypeHints::parse($type, $value);
        }

        return $value;
    }

    public function getValidType($value): ? string
    {
        if (empty($this->typeHints)) {
            return 'mixed';
        }

        if (is_null($value)) {
            return $this->nullable
                ? 'mixed'
                : null;
        }

        foreach ($this->getTypeHints() as $typeHint) {
            if (ParamTypeHints::validate($typeHint, $value) ) {
                return $typeHint;
            }
        }

        return null;
    }

    public function isValid($value): bool
    {
        $type = $this->getValidType($value);
        return isset($type);
    }


    public function getDefString(): string
    {
        $name = $this->getName();
        $types = $this->getTypeHints();
        $typesDef = empty($types)
            ? ''
            : ':' . implode('|', $types);

        return $name . $typesDef;
    }


}