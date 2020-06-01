<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Parameter;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IParamDefs implements ParamDefs
{

    /**
     * @var Param[]
     */
    protected $params = [];

    /**
     * IParamDefs constructor.
     * @param Param[] $params
     */
    public function __construct(array $params)
    {
        foreach ($params as $param) {
            $this->params[$param->getName()] = $param;
        }
    }

    public static function create(array $definition): ParamDefs
    {
        $params = [];
        foreach ($definition as $key => $val) {

            $param = static::buildParam($key, $val);
            $params[] = $param;
        }
        return new static($params);
    }


    protected static function buildParam(string $key, $val) : IParam
    {
        $explodedKey = explode(':', $key, 2);
        $field = $explodedKey[0];
        $typeHints = !empty($explodedKey[1])
            ? explode('|', $explodedKey[1])
            : [];

        return new IParam($field, $typeHints, $val);

    }



    public function count(): int
    {
        return count($this->params);
    }

    public function addParam(Param $param): void
    {
        $this->params[$param->getName()] = $param;
    }


    public function hasParam(string $name): bool
    {
        return isset($this->params[$name]);
    }

    public function getAllParams(): array
    {
        return $this->params;
    }

    public function getParam(string $name): ? Param
    {
        return $this->params[$name] ?? null;
    }

    public function keys(): array
    {
        return array_keys($this->params);
    }

    public function parse(array $values, bool $onlyDefined = false): array
    {
        $results = [];

        foreach ($this->params as $key => $param) {
            $results[$key] = $param->parse($values[$key] ?? null);
        }

        if (!$onlyDefined) {
            $results = $results + $values;
        }

        return $results;
    }

    public function getDefaults(): array
    {
        return array_map(
            function (Param $param) {
                return $param->getDefault();
            },
            $this->params
        );
    }

    public function getDefinitions(): array
    {
        $definitions = [];
        foreach ($this->params as $param) {
            $definitions[$param->getDefString()] = $param->getDefault();
        }

        return $definitions;
    }


    public function __destruct()
    {
        $this->params = [];
    }


}