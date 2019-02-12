<?php

/**
 * Class ContextData
 * @package Commune\Chatbot\Host\Context
 */

namespace Commune\Chatbot\Framework\Context;


use Commune\Chatbot\Framework\Conversation\Scope;

class ContextData
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $scope = [];

    private $depends = [];

    private $props = [];

    private $data = [];

    /**
     * @var bool
     */
    private $needSave;

    /**
     * @var string
     */
    private $contextName;

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
    }


    private function getDefaultData(array $definedData) : array
    {
        return $definedData;
    }

    private function generateProps(array $definedProps, array $props) : array
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
        return ['id', 'scope', 'depends', 'props', 'data'];
    }

    public function __wakeup()
    {
        $this->needSave = false;
    }

}