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

use Commune\Blueprint\CommuneEnv;
use Commune\Blueprint\Exceptions\CommuneLogicException;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Memory\Memory;
use Commune\Blueprint\Ghost\MindDef\MemoryDef;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
trait TRecollection
{
    /**
     * @var string
     */
    protected $_id;

    /**
     * @var MemoryDef
     */
    protected $_def;

    /**
     * @var Memory
     */
    protected $_memory;

    /**
     * @var Cloner
     */
    protected $_cloner;

    public function getId() : string
    {
        return $this->_id;
    }

    public function isLongTerm(): bool
    {
        return $this->_def->isLongTerm();
    }

    public function offsetExists($offset)
    {
        return $this->_memory->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        $value = $this->_memory->offsetGet($offset);

        if ($value instanceof Cloner\ClonerInstanceStub) {
            $value = $value->toInstance($this->_cloner);
        }

        return $value;
    }


    protected function warningOrException(string $error)
    {
        if (CommuneEnv::isDebug()) {
            $this->_cloner->logger->warning($error);
        } else {
            throw new CommuneLogicException($error);
        }
    }

    public function offsetSet($offset, $value)
    {
        // 暂时放弃了默认的类型校验了. 极大地增加复杂度, 收益却不大.

        // 变为 instance stub
        if ($value instanceof Cloner\ClonerInstance) {
            $value = $value->toInstanceStub();
        }

        $this->_memory->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->_memory->offsetUnset($offset);
    }

    public function toArray(): array
    {
        return $this->_memory->toArray();
    }

    public function toData()  : array
    {
        return array_filter($this->toArray(), function($value){
            return !is_object($value);
        });

    }

    public function getIterator()
    {
        $keys = $this->keys();
        $keys = array_flip($keys);
        foreach ($keys as $key => $index) {
            yield $this->offsetGet($key);
        }
    }

    public function keys() : array
    {
        return $this->_memory->keys();
    }

    public function __destruct()
    {
        $this->_cloner = null;
        $this->_memory = null;
        $this->_def = null;
    }
}