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

use Commune\Blueprint\Exceptions\HostLogicException;
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
        if ($this->_cloner->isDebugging()) {
            $this->_cloner->logger->warning($error);
        } else {
            throw new HostLogicException($error);
        }
    }

    public function offsetSet($offset, $value)
    {
        $params = $this->_def->getParams();
        $memoryName = $this->getName();

        // set undefined param
        if (!$params->hasParam($offset)) {

            // 线上环境日志提醒, 测试状态禁止.
            $error = "memory $memoryName try to set value for undefined parameter $offset";
            $this->warningOrException($error);
            $this->doSetValue($offset, $value);
            return;
        }

        $param = $params->getParam($offset);
        $type = $param->validate($value);

        if (is_null($type)) {
            // 线上环境日志提醒, 测试状态禁止.
            $error = "memory $memoryName try to set invalid value for parameter $offset";
            $this->warningOrException($error);
        }

        if (!empty($type)) {
            $value = $param->parse($value, $type);
        }

        $this->doSetValue($offset, $value);
        return;
    }

    protected function doSetValue($offset, $value) : void
    {
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

    public function __destruct()
    {
        $this->_cloner = null;
        $this->_memory = null;
        $this->_def = null;
    }
}