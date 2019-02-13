<?php

/**
 * Class ContextObj
 * @package Commune\Chatbot\Host\Context
 */

namespace Commune\Chatbot\Framework\Context;


use Commune\Chatbot\Framework\Character\Platform;
use Commune\Chatbot\Framework\Character\Recipient;
use Commune\Chatbot\Framework\Character\User;
use Commune\Chatbot\Framework\Context\Predefined\Answer;
use Commune\Chatbot\Framework\Context\Predefined\Choice;
use Commune\Chatbot\Framework\Context\Predefined\Confirmation;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Conversation\Scope;
use Commune\Chatbot\Framework\Conversation\Talkable;
use Commune\Chatbot\Framework\Support\ArrayAbleToJson;
use Commune\Chatbot\Framework\Directing\Location;
use Commune\Chatbot\Framework\Session\Session;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Message\Message;
use Commune\Chatbot\Framework\Message\Text;
use Illuminate\Support\Arr;

class Context implements Talkable,\ArrayAccess, \JsonSerializable
{

    use ArrayAbleToJson;
    /**
     * @var ContextData
     */
    protected  $data;

    /**
     * @var Session
     */
    protected  $session;

    /**
     * @var Conversation
     */
    protected  $conversation;

    /**
     * @var ContextCfg
     */
    protected  $config;


    public function __construct(ContextData $data, Session $session, ContextCfg $config)
    {
        $this->data = $data;
        $this->session = $session;
        $this->conversation = $session->getConversation();
        $this->config = $config;
    }

    public function fireEvent(string $name)
    {
        if (!in_array($name, ContextCfg::EVENTS)) {
            //todo
            throw new \BadMethodCallException();
        }

        $this->data->listenContextEvent($name);
        call_user_func([$this->config, $name], $this);
    }

    public function callConfigMethod(string $method, Intent $intent)
    {
        if (!method_exists($this->config, $method)) {
            //todo
            throw new \BadMethodCallException();
        }
        call_user_func([$this->config, $method], $this, $intent);
    }

    /*------- 特殊值 --------*/

    public function getName() : string
    {
        return $this->data->getContextName();
    }


    public function getId() : string
    {
        return $this->data->getId();
    }


    public function getScope() : array
    {
        return $this->data->getScope();
    }

    public function isAlive() : bool
    {
        return $this->data->ifAlive($this->config->getScopeTypes(), $this->conversation->getScope());
    }


    public function getLocation() : Location
    {
        return new Location($this->getName(), $this->data->getProps(), $this->data->getId());
    }

    public function getDataStatus() : int
    {
        return $this->data->getStatus();
    }

    /*--------- conversation ----------*/


    public function getSender() : User
    {
        return $this->conversation->getSender();
    }

    public function getRecipient() : Recipient
    {
        return $this->conversation->getRecipient();
    }

    public function getPlatform() : Platform
    {
        return $this->conversation->getPlatform();
    }

    /*------- getter -------*/

    public function fetch(string $key)
    {
        return Arr::get($this, $key);
    }

    public function get(string $name)
    {
        switch($name) {
            case 'contextName':
                return $this->getName();
            case 'contextId' :
                return $this->getId();
            case 'isAlive':
                return $this->isAlive();
            case 'scope' :
                return $this->data->getScope();
        }

        if ($this->config->dataSchemaExists($name)) {
            return $this->data->getDataVal($name);
        }

        if ($this->config->propsSchemaExists($name)) {
            return $this->data->getProp($name);
        }

        if ($this->config->mutatorSchemaExists($name)) {
            return $this->config->getter($this, $name);
        }

        if ($this->config->dependsSchemaExists($name)) {
            return $this->getDepend($name);
        }

        return null;
    }



    /*------- setter -------*/

    public function set(string $name, $value)
    {
        if ($this->config->dataSchemaExists($name)) {
            $this->data->setDataVal($name, $value);
        } elseif ($this->config->mutatorSchemaExists($name)) {
            $this->config->setter($this, $name, $value);
        } else {
            //todo
            throw new \BadMethodCallException();
        }
    }


    public function has(string $key) : bool
    {
        $val = $this->get($key);
        return isset($val);
    }

    public function del(string $key)
    {
        if ($this->config->dependsSchemaExists($key)) {
            $this->data->unsetDependencyId($key);

        } elseif ($this->config->dataSchemaExists($key)) {
            $this->data->unsetDataVal($key);
        }
    }

    /*------- depending -------*/

    public function initDepending() : ? Location
    {
        foreach ($this->config->getDependsSchema() as $name => $val) {
            $depend = $this->getDepend($name);
            if (!isset($depend)) {
                $id = $this->data->getDependencyId($name);
                $contextName = $val[0] ?? '';
                $definedProps = $val[1] ?? null;

                if (!isset($definedProps)) {
                    $props = [];
                } elseif (is_array($definedProps)) {
                    $props = $definedProps;
                } elseif (is_string($definedProps) && method_exists($this, $definedProps)) {
                    $props = $this->{$definedProps} ();
                } else {
                    //todo
                    throw new ConfigureException();
                }
                return new Location($contextName, $props, $id);
            }
        }
        return null;
    }

    protected function getDepend(string $name) : ? Context
    {
        $schema = $this->config->getDependOfSchema($name);
        $contextName = $schema[0];
        $id = $this->data->getDependencyId($name);

        if (!isset($id)) {
            $id = $this->session->makeContextId($contextName);
            $this->data->setDependencyId($name, $id);
        }

        return $this->session->fetchContextById($id);
    }

    /*------- array -------*/

    public function offsetExists($offset)
    {
        $val = $this->get($offset);
        return isset($val);
    }

    public function offsetGet($offset)
    {
//        $data = $this->get($offset);
//
//        if (isset($data) && Arr::accessible($data)) {
//            return new ArrayWrapper($data, $this, $offset);
//        }
//        return $data;
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->del($offset);
    }

    /*------- toArray -------*/

    public function toArray() : array
    {
        $result = [];
        $result['contextName'] = $this->getName();
        $result['contextId'] = $this->getId();
        $result['isAlive'] = $this->get('isAlive');

        foreach($this->get('scope') as $index => $value) {
            $result['scope'][Scope::getScopeName($index)] = $value;
        }

        foreach($this->config->getPropsSchema() as $key => $val) {
            $result[$key] = $this->wrapValue($this->get($key));
        }

        foreach($this->config->getDataSchema() as $key => $val) {
            $result[$key] = $this->wrapValue($this->get($key));
        }

        foreach($this->config->getDependsSchema() as $key => $value) {
            $result[$key] = $this->wrapValue($this->get($key));
        }
        return $result;
    }

    protected function wrapValue($value)
    {
        if (is_object($value) && method_exists($value, 'toArray')) {
            return $value->toArray();
        }
        return $value;
    }

    public function toJson(int $option = null) : string
    {
        $option = $option ?? JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT;
        return json_encode($this->toArray(), $option);
    }

    public function toString() : string
    {
        return $this->config->toString($this);
    }

    public function __toString()
    {
        return $this->toString();
    }

    /*------- talk -------*/

    public function format(string $temp, array $fields)
    {
        $params = [];
        foreach ($fields as $field) {
            $val = $this->fetch($field);
            if (is_null($val)) {
                $val = 'null';
            } elseif (is_bool($val)) {
                $val = $val ? 'true' : 'false';
            }
            $params[] = $val;
        }

        $temp = str_replace('{}', '%s', $temp);
        array_unshift($params, $temp);
        return call_user_func_array('sprintf', $params);
    }

    public function say(string $text, int $style, string $verbose = Message::NORMAL)
    {
        $message = new Text($text, Text::INFO, $verbose);
        $this->conversation->reply($message);
    }

    public function info(string $message, string $verbose = Message::NORMAL)
    {
        $this->say($message, Text::INFO, $verbose);
    }

    public function warn(string $message, string $verbose = Message::NORMAL)
    {
        $this->say($message, Text::WARN, $verbose);
    }

    public function error(string $message, string $verbose = Message::NORMAL)
    {
        $this->say($message, Text::ERROR, $verbose);
    }

    public function reply(Message $message)
    {
        $this->conversation->reply($message);
    }

    public function ask(string $callbackRouteName, string $question, string $default = null) : Location
    {
        $intended = $this->getLocation();
        $intended->setCallback($callbackRouteName);
        $answer = new Location(Answer::class, [
            'question' => $question,
            'default' => $default
        ]);
        $answer->setIntended($intended);
        return $answer;
    }


    public function confirm(string $callbackRouteName, string $question, string $default = 'yes') : Location
    {
        $intended = $this->getLocation();
        $intended->setCallback($callbackRouteName);
        $answer = new Location(Confirmation::class, [
            'question' => $question,
            'default' => $default
        ]);
        $answer->setIntended($intended);
        return $answer;
    }

    public function choose(string $callbackRouteName, string $question, array $choices, int $default = 0) : Location
    {
        $intended = $this->getLocation();
        $intended->setCallback($callbackRouteName);
        $to = new Location(Choice::class, [
            'question' => $question,
            'choices' => $choices,
            'default' => $default,
        ]);
        $to->setIntended($intended);
        return $to;
    }

    public function depend(string $callbackRouteName, string $contextName, array $props = []) : Location
    {
        $intended = $this->getLocation();
        $intended->setCallback($callbackRouteName);
        $to = new Location($contextName, $props);
        $to->setIntended($intended);
        return $to;
    }

}