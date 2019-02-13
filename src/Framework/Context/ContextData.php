<?php

/**
 * Class ContextData
 * @package Commune\Chatbot\Host\Context
 */

namespace Commune\Chatbot\Framework\Context;


use Commune\Chatbot\Framework\Conversation\Scope;

class ContextData
{

    const CREATED = 1;
    const WAKED = 2;
    const FUNCTION = 3;
    const SLEEP = 4;
    const DEAD = 5;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var array
     */
    protected $scope = [];

    protected $depends = [];

    protected $props = [];

    protected $data = [];

    /**
     * @var bool
     */
    protected $needSave;

    /**
     * @var string
     */
    protected $contextName;

    public function __construct(
        string $id,
        string $contextName,
        array $definedData,
        array $definedProps,
        Scope $scope,
        array $props
    )
    {
        $this->id = $id;
        $this->contextName = $contextName;
        $this->scope = $scope->toArray();
        $this->data = $this->getDefaultData($definedData);
        $this->props = $this->generateProps($definedProps, $props);
        $this->needSave = true;
        $this->status = self::CREATED;
    }


    protected function getDefaultData(array $definedData) : array
    {
        return $definedData;
    }

    protected function generateProps(array $definedProps, array $props) : array
    {
        return $props + $definedProps;
    }


    /*--------- getter ---------*/

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    public function ifAlive(array $scopeTypes, Scope $scope) : bool
    {
        foreach ($scopeTypes as $scopeType) {
            if (!isset($this->scope[$scopeType])) {
                return false;
            }

            if ( $this->scope[$scopeType] !== $scope->getScope($scopeType)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function getContextName(): string
    {
        return $this->contextName;
    }

    public function needSave() : bool
    {
        return $this->needSave;
    }

    public function getStatus() : int
    {
        return $this->status;
    }

    public function listenContextEvent(string $contextEvent)
    {
        switch ($contextEvent) {
            case ContextCfg::CREATING :
            case ContextCfg::WAKING :
            case ContextCfg::RESTORING :
                $this->status = self::FUNCTION;
                break;
            case ContextCfg::PREPARED :
            case ContextCfg::FAILED :
            case ContextCfg::CANCELED :
                $this->status = self::DEAD;
                break;
        }
    }

    /**
     * @return array
     */
    public function getScope(): array
    {
        return $this->scope;
    }


    /*--------- data ---------*/

    public function getData() : array
    {
        return $this->data;
    }

    public function getDataVal(string $name)
    {
        return $this->data[$name] ?? null;
    }

    public function setDataVal(string $name, $value)
    {
        $this->needSave = true;
        $this->data[$name] = $value;
    }

    public function unsetDataVal(string $name)
    {
        $this->needSave = true;
        unset($this->data[$name]);
    }

    /*--------- props ---------*/


    public function getProps() : array
    {
        return $this->props;
    }

    public function getProp(string $name)
    {
        return $this->props[$name] ?? null;
    }


    /*--------- depend ---------*/

    public function getDependencyId(string $name) : ? string
    {
        return $this->depends[$name] ?? null;
    }

    public function setDependencyId(string $name, string $id)
    {
        $this->needSave = true;
        $this->depends[$name] = $id;
    }

    public function unsetDependencyId(string $name)
    {
        $this->needSave = true;
        unset($this->depends[$name]);
    }

    /**
     * @return array
     */
    public function getDepends(): array
    {
        return $this->depends;
    }


    /*--------- serialize ---------*/


    public function toArray() : array
    {
        return [
            'id' => $this->getId(),
            'scope' => $this->getScope(),
            'depends' => $this->getDepends(),
            'props' => $this->getProps(),
            'data' => $this->getData(),
        ];
    }

    public function __sleep()
    {
        if ($this->status === self::FUNCTION) {
            $this->status = self::SLEEP;
        }

        return [
            'id',
            'status',
            'scope',
            'depends',
            'props',
            'contextName',
            'data',
        ];
    }

    public function __wakeup()
    {
        $this->needSave = false;
        if ($this->status === self::SLEEP) {
            $this->status = self::WAKED;
        }
    }

}