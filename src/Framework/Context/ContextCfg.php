<?php

/**
 * Class ContextCfg
 * @package Commune\Chatbot\Host\Context
 */

namespace Commune\Chatbot\Framework\Context;


use Commune\Chatbot\Framework\Conversation\Scope;
use Commune\Chatbot\Framework\Intent\Predefined\ArrayIntent;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Message\Message;
use Commune\Chatbot\Framework\Routing\DialogRoute;

abstract class ContextCfg
{
    const SCOPE = [
        Scope::SESSION
    ];

    const DESCRIPTION = '';

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

    //todo 不要放在这里, 反而不好写后续代码
    const CREATING = 'creating';
    const WAKING = 'waking';
    const DEPENDING = 'depending';
    const RESTORING  = 'restoring';
    const PREPARED = 'prepared';
    const FAILED = 'failed';
    const CANCELED = 'canceled';

    const EVENTS = [
        self::CREATING,
        self::WAKING,
        self::DEPENDING,
        self::RESTORING,
        self::PREPARED,
        self::FAILED,
        self::CANCELED,
    ];


    private $scopeTypes;

    abstract public function routing(DialogRoute $route);

    public function creating(Context  $context) {}

    public function depending(Context $context) {}

    public function restoring(Context  $context) {}

    public function waking(Context  $context) {}

    public function prepared(Context  $context) {}

    public function failed(Context  $context) {}

    public function canceled(Context  $context) {}

    public function toString(Context $context) : string
    {
        return $context->toJson();
    }

    public function toIntent(Context $context, Message $message) : Intent
    {
        return new ArrayIntent(
            $context->getId(),
            $message,
            $context->toEntities()
        );
    }

    /*------ validate ------*/

    public function validateDepend(string $key, Context $depend, Context $self) : bool
    {
        if (method_exists($this, $method = 'validate'.ucfirst($key))) {
            return call_user_func([$this, $method], $depend, $self);
        }
        return true;
    }

    public function getPropsForDepend(string $name, Context $self) : array
    {
        if (method_exists($this, $method = 'prop'.ucfirst($name))) {
            return call_user_func([$this, $method], $self);
        }
        return [];
    }

    public function fetchEntities(Context $context)
    {
        return $context->getData();
    }


    /*------ schema ------*/

    public function dataSchemaExists(string $name) : bool
    {
        return array_key_exists($name, static::DATA);
    }

    public function getDataSchema() : array
    {
        return static::DATA;
    }

    public function propsSchemaExists(string $name) : bool
    {
        return array_key_exists($name, static::PROPS);
    }

    public function getPropsSchema() : array
    {
        return static::PROPS;
    }

    public function mutatorSchemaExists(string $name) : bool
    {
        return in_array($name, static::MUTATOR);
    }

    public function dependsSchemaExists(string $name) : bool
    {
        return array_key_exists($name, static::DEPENDS);
    }

    public function getDependsSchema() : array
    {
        return static::DEPENDS;
    }

    public function getDependOfSchema(string $name) :array
    {
        return static::DEPENDS[$name] ?? [];
    }

    public function getDescription(Context $context) : string
    {
        return static::DESCRIPTION;
    }

    /*------ getter ------*/

    public function getter(Context $context, string $name)
    {
        $method = 'get'.ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->{$method}($context);
        }
        return null;
    }

    public function setter(Context $context, string $name, $value)
    {
        $method = 'set'.ucfirst($name);
        if (method_exists($this, $method)) {
            $this->{$method}($context, $value);
        }
    }

    /*------ 属性 ------*/

    public function getScopeTypes() : array
    {
        if (isset($this->scopeTypes)) {
            return $this->scopeTypes;
        }
        $this->scopeTypes = static::SCOPE;
        sort($this->scopeTypes);
        return $this->scopeTypes;
    }

}