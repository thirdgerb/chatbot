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
        $manager = $this->_def->getParams();
        $memoryName = $this->getName();

        // set undefined param
        if (!$manager->hasParam($offset)) {


            $error = "memory $memoryName try to set value for undefined parameter $offset";
            $this->warningOrException($error);

            if ($value instanceof Cloner\ClonerInstance) {
                $value = $value->toInstanceStub();
            }

            $this->_memory->offsetSet($offset, $value);
            return;
        }

        $param = $manager->getParam($offset);

        // parse
        $parser = $param->getValParser();
        $value = isset($parser)
            ? $parser($value)
            : $value;

        // validate
        $validator = $param->getTypeValidator();
        if (isset($validator) && !$validator($value)) {
            $error = "memory $memoryName try to set invalid value for parameter $offset";
            $this->warningOrException($error);
        }

        if ($value instanceof Cloner\ClonerInstance) {
            $value = $value->toInstanceStub();
        }

        $this->_memory->offsetSet($offset, $value);
        return;
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