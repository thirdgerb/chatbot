<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Memory;

use Commune\Blueprint\Ghost\Memory;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Support\Arr\TArrayData;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IMemory implements Memory
{
    use ArrayAbleToJson, TArrayData;

    /**
     * @var string
     */
    protected $_id;

    /**
     * @var bool
     */
    protected $_longTerm;


    /**
     * IMemory constructor.
     * @param string $id
     * @param bool $longTerm
     * @param array $stub
     */
    public function __construct(string $id, bool $longTerm, array $stub)
    {
        $this->_id = $id;
        $this->_longTerm = $longTerm;
        $this->_data = $stub;
    }


    public function getId(): string
    {
        return $this->_id;
    }

    public function isChanged(): bool
    {
        return $this->_changed;
    }

    public function isLongTerm(): bool
    {
        return $this->_longTerm;
    }

    public function toArray(): array
    {
        return array_map(function($val) {
            return $val instanceof ArrayAndJsonAble
                ? $val->toArray()
                : $val;
        }, $this->_data);
    }

    public function toData(): array
    {
        return $this->_data;
    }


    public function __sleep()
    {
        return [
            '_id',
            '_longTerm',
            '_data',
        ];
    }

    public function __wakeup()
    {
        $this->_changed = false;
    }

}