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

use Commune\Support\Utils\TypeUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IStructProperty extends AStructProperty
{

    protected function softParse($value)
    {
        // 用默认规则进行数据类型的弱类型转换.
        if ($this->ruleCounts === 1) {
            $rule = $this->rules[0];
            return TypeUtils::parse($rule, $value);
        }

        return $value;
    }


    protected function validateAllRules($value) :  ? string
    {
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
        $name = $this->name;
        return "[$name] not fit rules " . implode('|', $this->rules) . ", $errorMsg";
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

}