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
     * @var bool
     */
    protected $list;

    /**
     * @var mixed
     */
    protected $default;

    /**
     * IParam constructor.
     * @param string $name
     * @param bool $list
     * @param mixed $default
     */
    public function __construct(string $name, bool $list, $default)
    {
        $this->name = $name;
        $this->list = $list;
        $this->default = $default;
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function isList(): bool
    {
        return $this->list;
    }

    public function parse($value)
    {
        if ($this->isList()) {
            return Arr::wrap($value);
        }

        return (is_array($value) ? current($value) : $value)
            ?? $this->getDefault();
    }

    public function getDefault()
    {
        return $this->default;
    }


}