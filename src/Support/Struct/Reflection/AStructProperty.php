<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Struct\Reflection;

use Commune\Support\Struct\InvalidStructException;
use Commune\Support\Struct\Struct;
use Commune\Support\Utils\TypeUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
abstract class AStructProperty implements StructProperty
{
    /**
     * @var string
     */
    protected $structName;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string[]
     */
    protected $rules = [];

    /**
     * 允许为 null
     * @var bool
     */
    protected $nullable = false;

    /**
     * 关联类名
     * @var null
     */
    protected $relationClass = null;

    /**
     * 是否是数组式的关联关系.
     * @var bool
     */
    protected $isListRelation = false;

    /**
     * @var int
     */
    protected $ruleCounts;

    /**
     * @var string
     */
    protected $desc;


    /**
     * @var string
     */
    protected $query;

    /**
     * @var mixed
     */
    protected $default;


    /**
     * @var bool|null
     */
    protected $isList;

    /**
     * IStructProperty constructor.
     * @param string $structName
     * @param string $propertyName
     * @param array $rules
     * @param null|string $relationClass
     * @param bool $isListRelation
     * @param mixed $default
     * @param string $desc
     * @param string $query
     */
    public function __construct(
        string $structName,
        string $propertyName,
        array $rules,
        ?string $relationClass,
        bool $isListRelation,
        $default = null,
        string $desc = '',
        string $query = ''
    )
    {
        $this->structName = $structName;
        $this->name = $propertyName;
        $this->nullable = in_array('null', $rules);
        $this->rules = array_values(array_diff($rules, ['null']));
        $this->relationClass = $relationClass;
        $this->isListRelation = $isListRelation;
        $this->ruleCounts = count($this->rules);
        $this->default = $default;
        $this->desc = $desc;
        $this->query = $query;
    }


    public function getName() : string
    {
        return $this->name;
    }


    public function getStructName() : string
    {
        return $this->structName;
    }

    public function getDesc(): string
    {
        return $this->desc;
    }

    public function getQuery(): string
    {
        if (empty($this->query)) {
            $name = $this->getName();
            $desc = $this->getDesc();
            return "请输入 [$name]($desc)";
        }
        return $this->query;
    }

    public function allowNulls(): bool
    {
        return $this->nullable;
    }

    public function getDefaultValue()
    {
        return $this->default;
    }

    public function isList(): bool
    {
        if (isset($this->isList)) {
            return $this->isList;
        }

        if (isset($this->relationClass)) {
            return $this->isList = $this->isListRelation;
        }

        foreach ($this->rules as $rule) {
            if (!TypeUtils::isListTypeHint($rule)) {
                return $this->isList = false;
            }
        }

        return $this->isList = true;
    }


    public function isRelation(): bool
    {
        return isset($this->relationClass);
    }

    public function getRelationClass(): ? string
    {
        return $this->relationClass;
    }

    public function getValidateRules(): array
    {
        $rules = $this->rules;
        if ($this->nullable) {
            $rules[] = 'null';
        }
        if ($this->relationClass) {
            $rules[] = $this->isListRelation
                ? $this->relationClass . '[]'
                : $this->relationClass;
        }

        return $rules;
    }

    public function getTypeHint(): string
    {
        return implode('|', $this->getValidateRules());
    }

    /*------- getter setter -------*/

    public function set(Struct $struct, $value): void
    {
        $name = $this->getName();
        $struct->{$name} = $value;
    }

    public function get(Struct $struct)
    {
        $name = $this->getName();
        return $struct->{$name};
    }

    /*------- parse -------*/

    abstract protected function softParse($value);


    public function parseValue($value, bool $strict = true)
    {
        if ($this->isRelation()) {
            return $this->parseRelation($value);
        }

        // 强类型下不进行过滤.
        if ($strict) {
            return $value;
        }

        // null 的话赋予默认值
        if (is_null($value) && !$this->allowNulls() && isset($this->default)) {
            return $this->default;
        }

        return $this->softParse($value);
    }



    /**
     * @param $value
     * @return Struct[]|Struct|null
     */
    protected function parseRelation($value)
    {
        if (is_null($value)) {
            return $value;
        }

        $structType = $this->relationClass;
        $isList = $this->isListRelation;

        if (!$isList) {
            return $this->buildRelatedStruct($structType, $value);

        } elseif ($isList && is_array($value)) {
            $result = [];
            foreach ($value as $key => $val) {
                // null
                if (is_null($val)) {
                    continue;
                }

                // build
                $result[$key] = $this->buildRelatedStruct($structType, $val);
            }

            return $result;
        }


        $expect = $this->relationClass;
        $actual = TypeUtils::getType($value);

        throw $this->error("set relation value failed, expect $expect or array, $actual given");
    }

    /**
     * @param string $error
     * @return InvalidStructException
     */
    protected function error(string $error) : InvalidStructException
    {
        $structName = $this->getStructName();
        $field = $this->getName();

        return new InvalidStructException("struct $structName property $field error: $error");
    }

    protected function buildRelatedStruct(string $type, $data) : Struct
    {
        // 如果是封装好的对象.
        if (is_object($data) && is_a($data, $type, TRUE)) {
            return $data;
        }

        // 居然不是数组.
        if (!is_array($data)) {
            $type = TypeUtils::getType($data);
            $class = $this->relationClass;
            throw $this->error("related property only accept array or $class, $type given");
        }

        return call_user_func(
            [$type, 'create'],
            $data
        );
    }


    /*------- validate -------*/

    abstract protected function validateAllRules($value) : ? string;

    public function validateValue($value) : ? string
    {
        // 如果是关联关系
        if (isset($this->relationClass)) {
            return $this->relationValidate($value);
        }

        if (is_null($value)) {
            if ($this->nullable) {
                return null;
            }
            return $this->name . ' is null which is not allowed';
        }

        return $this->validateAllRules($value);
    }


    protected function relationValidate($value) : ? string
    {
        $name = $this->getName();
        $relation = $this->getRelationClass();

        // relation 是否允许为 null.
        if (is_null($value)) {
            return $this->nullable ? null : $name . ' should not be null';
        }

        // 是列表对象
        if ($this->isListRelation) {
            if (!is_array($value)) {
                return $name . ' should be a list of ' . $relation;
            }

            foreach ($value as $i => $val) {

                if (!is_a($val, $relation, TRUE)) {
                    return $name . ' element ' .$i .'should be instance of ' . $this->relationClass;
                }
            }

            return null;
        }

        // 不是列表对象
        return is_a($value, $relation, TRUE)
            ? null
            : $name . ' is not valid instance of ' . $this->relationClass;
    }



}