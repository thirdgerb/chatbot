<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context\Params;

use Commune\Blueprint\Ghost\Context\Param;
use Commune\Blueprint\Ghost\Context\ParamTypeHints;
use Illuminate\Support\Arr;

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

        } else {
            foreach ($typeHints as $typeHint) {
                if ($typeHint === 'null') {
                    $this->nullable = true;
                    continue;
                }

                if ($typeHint === 'mixed') {
                    $this->nullable = true;
                    $this->typeHints = [];
                    break;
                }

                $this->typeHints[] = $typeHint;
            }
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

        $type = $type ?? $this->validate($value);
        return ParamTypeHints::parse($type, $value);
    }

    public function validate($value): ? string
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


}