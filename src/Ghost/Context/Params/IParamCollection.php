<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context\Params;

use Commune\Blueprint\Ghost\Context\Param;
use Commune\Blueprint\Ghost\Context\ParamCollection;
use Commune\Support\Utils\TypeUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IParamCollection implements ParamCollection
{

    /**
     * @var array
     */
    protected $definitions;

    /**
     * @var Param[]
     */
    protected $params = [];

    public function __construct(array $definitions)
    {
        $this->definitions = $definitions;

        foreach ($definitions as $key => $val) {

            $param = $this->buildParam($key, $val);
            $name = $param->getName();
            $this->params[$name] = $param;
        }
    }

    protected function buildParam(string $key, $val) : IParam
    {
        $explodedKey = explode(':', $key, 2);
        $field = $explodedKey[0];
        $typeHints = !empty($explodedKey[1])
            ? explode('|', $explodedKey[1])
            : [];

        return new IParam($field, $typeHints, $val);

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

    public function parse(array $values, bool $strict = false): array
    {
        $results = [];

        foreach ($this->params as $key => $param) {
            $results[$key] = $param->parse($values[$key] ?? null);
        }

        if (!$strict) {
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
        return $this->definitions;
    }


    public function __destruct()
    {
        $this->params = [];
    }


}