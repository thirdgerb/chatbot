<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IMindDef;

use Commune\Blueprint\Ghost\MindDef\DefParam;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Ghost\MindMeta\Option\ParamOption;
use Commune\Support\Utils\TypeUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IDefParam implements DefParam
{

    /**
     * @var ParamOption
     */
    protected $option;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $isList;

    /**
     * IContextParameter constructor.
     * @param ParamOption $option
     */
    public function __construct(ParamOption $option)
    {
        $this->option = $option;
        $name = $option->name;
        if (substr($name, -2, 2) === '[]') {
            $this->name = substr($name, 0, -2);
            $this->isList = true;
        } else {
            $this->name = $name;
            $this->isList = true;
        }
    }


    public function getName(): string
    {
        return $this->name;
    }

    public function getTypeValidator(): ? callable
    {
        $type = $this->option->type;
        $isList = $this->isList();
        switch($type) {
            case 'string':
            case 'str':
                return $this->makeValidator('is_string', $isList);
            case 'bool':
            case 'boolean':
                return $this->makeValidator('is_bool', $isList);
            case 'int':
            case 'integer':
                return $this->makeValidator('is_int', $isList);
            case 'float':
            case 'double':
                return $this->makeValidator('is_numeric', $isList);
            case 'array':
                return $this->makeValidator('is_array', $isList);
        }

        if (is_callable($type)) {
            return $type;
        }

        if (TypeUtils::isCallableClass($type)) {
            return [$type, '__invoke'];
        }

        return null;
    }

    protected function makeValidator(callable $validator, bool $isList) : callable
    {
        if (!$isList) {
            return $validator;
        }

        return function($value) use ($validator) : bool {

            if (!is_array($value)) {
                return false;
            }

            foreach ($value as $val) {
                $bool = call_user_func($validator, $val);
                if (!$bool) {
                    return false;
                }
            }

            return true;
        };
    }

    public function getValParser(): callable
    {
        $parser = $this->option->parser;

        if (is_callable($parser)) {
            return $parser;
        }

        if (TypeUtils::isCallableClass($parser)) {
            return [$parser, '__invoke'];
        }

        $type = $this->option->type;
        $isList = $this->isList();

        switch($type) {
            case 'string':
            case 'str':
                return $this->makeParser('strval', $isList);
            case 'bool':
            case 'boolean':
                return $this->makeParser('boolval', $isList);
            case 'int':
            case 'integer':
                return $this->makeParser('intval', $isList);
            case 'float':
                return $this->makeParser('floatval', $isList);
            case 'double':
                return $this->makeParser('doubleval', $isList);
        }

        return null;
    }

    protected function makeParser(callable $parser, bool $isList) : callable
    {
        if (!$isList) {
            return function($value) use ($parser) {
                if (is_array($value)) {
                    $value = current($value);
                }

                return call_user_func($parser, $value);
            };
        }

        return function($value) use ($parser) : array {
            if (!is_array($value)) {
                $value = [$value];
            }

            return array_map(function($val) use ($parser) {
                return call_user_func($parser, $val);
            }, $value);

        };

    }


    public function isList(): bool
    {
        return $this->isList;
    }

    public function getDefault()
    {
        return $this->option->default;
    }

    public function getQuery(): string
    {
        return $this->option->query;
    }

    public function asStage(): StageDef
    {
        // TODO: Implement asStage() method.
    }


}