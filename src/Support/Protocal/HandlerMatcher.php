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

use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class HandlerMatcher
{

    protected $matchers = [];

    /**
     * HandlerMatcher constructor.
     * @param HandlerOption[] $options
     */
    public function __construct(array $options = [])
    {
        foreach ($options as $option) {
            $this->addOption($option);
        }
    }

    public function addOption(HandlerOption $option) : void
    {
        $group = $option->group;
        $protocal = $option->protocal;
        $handler = $option->handler;
        $filter = $option->filter;
        $this->matchers[$group][] = [$protocal, $filter, $handler];
    }

    public function matchHandler(string $group, Protocal $protocal) : ? string
    {
        if (empty($this->matchers[$group])) {
            return null;
        }

        $matchers = $this->matchers[$group];

        foreach ($matchers as list($protocalName, $filterRules, $handlerName)) {

            if (!is_a($protocal, $protocalName, TRUE)) {
                continue;
            }

            if ($this->match($protocal->getProtocalId(), $filterRules)) {
                return $handlerName;
            }
        }

        return null;
    }

    public function match(string $protocalId, array $rules) : bool
    {
        foreach ($rules as $rule) {

            if ($rule === '*') {
                return true;
            }

            $matched = StringUtils::isWildcardPattern($rule)
                ? StringUtils::wildcardMatch($rule, $protocalId, '\w+')
                : $rule === $protocalId;

            if ($matched) {
                return true;
            }
        }

        return false;
    }
}
