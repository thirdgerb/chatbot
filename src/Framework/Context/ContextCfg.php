<?php

/**
 * Class ContextCfg
 * @package Commune\Chatbot\Host\Context
 */

namespace Commune\Chatbot\Framework\Context;


use Commune\Chatbot\Framework\Conversation\Scope;
use Commune\Chatbot\Framework\Routing\DialogRoute;

abstract class ContextCfg
{
    const SCOPE = [
        Scope::SESSION
    ];

    const DEPENDS = [

    ];

    const DATA = [
        //'key' => 'value'
    ];

    const PROPS = [
        //'key' => 'val'
    ];

    const MUTATOR = [
        //'valueName',
    ];

    const CREATED_HOOK = 'created';
    const WAKED_HOOK = 'waked';
    const PREPARED_HOOK = 'prepared';
    const RESTORED_HOOK = 'restored';
    const FAILED_HOOK = 'failed';
    const CANCELED_HOOK = 'canceled';


    private $scopeTypes;

    abstract public function routing(DialogRoute $route);

    public function created(Context  $context) {}

    public function waked(Context  $context) {}

    public function prepared(Context  $context) {}

    public function restored(Context  $context) {}

    public function failed(Context  $context) {}

    public function canceled(Context  $context) {}

    /*------ validate ------*/

    final public function validate() :bool
    {
        //todo 未来要实现自检流程. 抛出配置异常.
        return true;
    }

    /*------ schema ------*/

    final public function dataSchemaExists(string $name) : bool
    {
        return array_key_exists($name, static::DATA);
    }
    final public function getDataSchema() : array
    {
        return static::DATA;
    }

    final public function propsSchemaExists(string $name) : bool
    {
        return array_key_exists($name, static::PROPS);
    }

    final public function getPropsSchema() : array
    {
        return static::PROPS;
    }

    final public function mutatorSchemaExists(string $name) : bool
    {
        return array_key_exists($name, static::MUTATOR);
    }

    final public function dependsSchemaExists(string $name) : bool
    {
        return array_key_exists($name, static::DEPENDS);
    }

    final public function getDependsSchema() : array
    {
        return static::DEPENDS;
    }

    final public function getDependOfSchema(string $name) :array
    {
        return static::DEPENDS[$name] ?? [];
    }


    /*------ getter ------*/

    final public function getter(Context $context, string $name)
    {
        $method = 'get'.ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->{$method}($context);
        }
        return null;
    }

    final public function setter(Context $context, string $name, $value)
    {
        $method = 'set'.ucfirst($name);
        if (method_exists($this, $method)) {
            $this->{$method}($context, $value);
        }
    }

    /*------ 属性 ------*/

    final public function getScopeTypes() : array
    {
        if (isset($this->scopeTypes)) {
            return $this->scopeTypes;
        }
        $this->scopeTypes = static::SCOPE;
        sort($this->scopeTypes);
        return $this->scopeTypes;
    }




}