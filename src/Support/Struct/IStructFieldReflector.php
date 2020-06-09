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
        $this->rules = array_values(array_diff($rules, ['null']));
        $this->relationClass = $relationClass;
        $this->isListRelation = $isListRelation;
        $this->ruleCounts = count($this->rules);
    }

    public function filterValue($value)
    {
        if ($this->ruleCounts === 1) {
            $rule = $this->rules[0];
            return TypeUtils::parse($rule, $value);
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

        if (is_null($value)) {
            if ($this->nullable) {
                return null;
            }
            return $this->fieldName . ' is null which is not allowed';
        }

        // 否则检查类型.
        $errors = [];
        foreach ($this->rules as $rule) {
            // 满足任意条件.
            $error = $this->validateRule($rule, $value);
            if (is_null($error)) {
                return null;
            }
            $errors[] = $error;
        }

        $errorMsg = implode(",", $errors);
        return 'type should fit rules ' . implode('|', $this->rules) . ", $errorMsg";

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
        return TypeUtils::isA($value, $this->relationClass);
    }

    /**
     * 递归地校验是否符合规则
     * @param string $rule
     * @param $value
     * @return null|string
     */
    protected function validateRule(string $rule, $value) : ? string
    {
        if (TypeUtils::isListTypeHint($rule)) {
            $rule = TypeUtils::pureListTypeHint($rule);
            return $this->listValidateRule($rule, $value);
        }

        return TypeUtils::validate($rule, $value)
            ? null
            : "{$rule} required, " . TypeUtils::getType($value) . ' given';
    }

    protected function listValidateRule(string $rule, $value) : ? string
    {
        if (!is_array($value)) {
            return "{$rule}[] require array, " . TypeUtils::getType($value) . ' given';
        }

        // 空数组的情况.
        if (empty($value)) {
            return null;
        }

        foreach ($value as $val) {
            $error = $this->validateRule($rule, $val);
            if (isset($error)) {
                return "{$rule}[] require each one as $rule, " . $error;
            }
        }
        return null;

    }


    public function getRules(): array
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

    public function getTypes(): string
    {
        return implode('|', $this->getRules());
    }


}