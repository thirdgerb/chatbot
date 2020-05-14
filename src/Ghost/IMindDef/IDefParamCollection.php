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
use Commune\Blueprint\Ghost\MindDef\DefParam;
use Commune\Blueprint\Ghost\MindDef\DefParamsCollection;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IDefParamCollection extends Collection implements DefParamsCollection
{

    /**
     * IDefParamCollection constructor.
     * @param DefParam[] $params
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

    public function getParam(string $name): DefParam
    {
        return $this->get($name);
    }

    /**
     * @return DefParam[]
     */
    public function getAllParams(): array
    {
        return $this->all();
    }

    public function parseValues(array $values): array
    {
        $data = [];
        foreach ($this->getAllParams() as $name => $param) {
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
        return $this->map(function(DefParam $param){
            return $param->getDefault();
        })->all();
    }

    public function __destruct()
    {
        $this->items = [];
    }


}