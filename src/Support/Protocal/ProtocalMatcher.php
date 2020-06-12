<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Protocal;

use Commune\Support\Option\Option;
use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ProtocalMatcher
{

    /**
     * @var ProtocalHandlerOpt[]
     */
    protected $matchers = [];

    /**
     * HandlerMatcher constructor.
     * @param ProtocalHandlerOpt[] $options
     */
    public function __construct(array $options = [])
    {
        foreach ($options as $option) {
            $this->addOption($option);
        }
    }

    public function addOption(ProtocalHandlerOpt $option) : void
    {
        $id = $option->getId();
        $this->matchers[$id] = $option;
    }

    /**
     * @param Protocal $protocal
     * @return \Generator|ProtocalHandlerOpt[]
     */
    public function matchEach(Protocal $protocal) : \Generator
    {
        if (!empty($this->matchers)) {
            foreach ($this->matchers as $option) {
                $protocalName = $option->protocal;
                if (!is_a($protocal, $protocalName, TRUE)) {
                    continue;
                }

                $filterRules = $option->filters;
                if ($this->match($protocal->getProtocalId(), $filterRules)) {
                    yield $option;
                }
            }
        }
    }

    public function matchFirst(Protocal $protocal) : ? ProtocalHandlerOpt
    {
        if (!empty($this->matchers)) {
            foreach ($this->matchers as $option) {
                $protocalName = $option->protocal;
                if (!is_a($protocal, $protocalName, TRUE)) {
                    continue;
                }

                $filterRules = $option->filters;
                $protocalId = $protocal->getProtocalId();

                if ($this->match($protocalId, $filterRules)) {
                    return $option;
                }
            }
        }

        return null;
    }

    public function match(string $protocalId, array $rules) : bool
    {
        if (empty($rules)) {
            return true;
        }

        foreach ($rules as $rule) {

            if ($rule === '*') {
                return true;
            }

            $matched = StringUtils::isWildcardPattern($rule)
                // 只匹配字母的情况. 暂时不做更复杂的匹配逻辑.
                ? StringUtils::wildcardMatch($rule, $protocalId, '\w+')
                : $rule === $protocalId;

            if ($matched) {
                return true;
            }
        }

        return false;
    }
}
