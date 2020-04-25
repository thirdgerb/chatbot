<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Struct;

use Commune\Support\Utils\TypeUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
class IStructFieldReflector implements StructFieldReflector
{
    /**
     * @var string
     */
    protected $structType;

    /**
     * @var string
     */
    protected $fieldName;

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
     * ReflectionFieldValidator constructor.
     * @param string $structType
     * @param string $fieldName
     * @param string[] $rules
     * @param string|null $relationClass
     * @param bool $isListRelation
     */
    public function __construct(
        string $structType,
        string $fieldName,
        array $rules,
        ?string $relationClass,
        bool $isListRelation
    )
    {
        $this->structType = $structType;
        $this->fieldName = $fieldName;
        $this->nullable = in_array('null', $rules);
        $this->rules = array_diff($rules, ['null']);
        $this->relationClass = $relationClass;
        $this->isListRelation = $isListRelation;
        $this->ruleCounts = count($this->rules);
    }

    public function filterValue($value)
    {
        if (is_scalar($value) && $this->ruleCounts === 1) {
            $rule = $this->rules[0];

            switch($rule) {
                case 'mixed' :
                    return $value;
                case 'string' :
                    return strval($value);
                case 'bool' :
                case 'boolean' :
                    return boolval($value);
                case 'int' :
                case 'integer' :
                    return intval($value);
                case 'float' :
                    return floatval($value);
                case 'double' :
                    return doubleval($value);
                default:
                    return $value;
            }
        }

        return $value;
    }


    public function getStructType() : string
    {
        return $this->structType;
    }

    public function getFieldName() : string
    {
        return $this->fieldName;
    }

    public function validateValue($value) : ? string
    {
        // 如果是关联关系
        if (isset($this->relationClass)) {
            return $this->relationValidate($value);
        }

        // 否则检查类型.
        foreach ($this->rules as $rule) {
            // 满足任意条件.
            if ($this->validateRule($rule, $value)) {
                return null;
            }
        }
        $type = TypeUtils::getType($value);
        return $this->fieldName . ' is invalid, type should be ' . implode('|', $this->rules) . ", $type given";

    }

    protected function relationValidate($value) : ? string
    {
        if (is_null($value)) {
            return $this->nullable ? null : $this->fieldName . ' should not be null';
        }

        // 是列表对象
        if ($this->isListRelation) {
            if (!is_array($value)) {
                return $this->fieldName . ' should be a list of ' . $this->relationClass;
            }

            foreach ($value as $val) {
                if (!$this->isRelationObj($val)) {
                    return $this->fieldName . ' element should be instance of ' . $this->relationClass;
                }
            }

            return null;
        }

        // 不是列表对象
        return $this->isRelationObj($value)
            ? null
            : $this->fieldName . ' is not valid instance of ' . $this->relationClass;
    }

    protected function isRelationObj($value) : bool
    {
        return is_object($value) && is_a($value, $this->relationClass, TRUE);
    }

    /**
     * 递归地校验是否符合规则
     * @param string $rule
     * @param $value
     * @return bool
     */
    protected function validateRule(string $rule, $value) : bool
    {
        $isList = substr($rule, -2, 2) === '[]';

        if (!$isList) {
            return $this->validateSingleRule($rule, $value);
        }

        if (!is_array($value)) {
            return false;
        }

        $rule = substr($rule, 0 , -2);
        foreach ($value as $val) {
            if ($this->validateRule($rule, $val)) {
                return true;
            }
        }
        return false;
    }

    protected function validateSingleRule(string $rule, $value) : bool
    {
        if (class_exists($rule)) {
            return is_object($value) && is_a($value, $rule, TRUE);
        }

        switch($rule) {
            case 'mixed' :
                return true;
            case 'string' :
                return is_string($value);
            case 'bool' :
            case 'boolean' :
                return is_bool($value);
            case 'int' :
            case 'integer' :
                return is_int($value);
            case 'float' :
                return is_float($value);
            case 'double' :
                return is_double($value);
            case 'array' :
                return is_array($value);
            case 'callable' :
                return is_callable($value);
            default:
                return false;
        }
    }

    public function getRules(): array
    {
        $rules = $this->rules;
        if ($this->nullable) {
            $rules[] = 'null';
        }
        if ($this->relationClass) {
            $rules[] = $this->isListRelation ? $this->relationClass . '[]' : $this->relationClass;
        }

        return $rules;
    }

    public function getTypes(): string
    {
        return implode('|', $this->getRules());
    }


}