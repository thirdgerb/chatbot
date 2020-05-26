<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context;

use Illuminate\Support\Collection;
use Commune\Blueprint\Ghost\MindDef\ParamDef;
use Commune\Blueprint\Ghost\MindDef\ParamDefCollection;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IParamDefCollection extends Collection implements ParamDefCollection
{

    /**
     * IDefParamCollection constructor.
     * @param ParamDef[] $params
     */
    public function __construct(array $params)
    {
        $items = [];
        foreach ($params as $param) {
            $items[$param->getName()] = $param;
        }

        parent::__construct($items);
    }

    public function getParamNames(): array
    {
        return array_keys($this->items);
    }


    public function hasParam(string $name): bool
    {
        return $this->has($name);
    }

    public function getParam(string $name): ParamDef
    {
        return $this->get($name);
    }

    /**
     * @return ParamDef[]
     */
    public function getAllParams(): array
    {
        return $this->all();
    }

    public function parseValues(array $values): array
    {
        $data = [];
        $params = $this->getAllParams();
        if (empty($params)) {
            return [];
        }

        foreach ($params as $name => $param) {
            $value = null;
            if (isset($values[$name])) {
                $parser = $param->getValParser();
                $value = isset($parser)
                    ? $parser($values[$name])
                    : $values[$name];
            }
            $data[$name] = $value ?? $param->getDefault();
        }

        return $data;
    }

    public function getDefaultValues(): array
    {
        return $this->map(function(ParamDef $param){
            return $param->getDefault();
        })->all();
    }

    public function countParams(): int
    {
        return $this->count();
    }


    public function __destruct()
    {
        $this->items = [];
    }


}