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
use Psr\Log\LoggerInterface;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ProtocalMatcher
{

    /**
     * @var ProtocalOption[]
     */
    protected $options = [];

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ProtocalMatcher constructor.
     * @param array $options
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger, array $options = [])
    {
        $this->logger = $logger;
        foreach ($options as $option) {
            $this->addOption($option);
        }
    }

    public function addOption(ProtocalOption $option) : void
    {
        $id = $option->getId();
        $this->options[$id] = $option;
    }

    /**
     * @param Protocal $protocal
     * @param string|null $interface
     * @return \Generator|HandlerOption[]
     */
    public function matchEach(Protocal $protocal, string $interface = null) : \Generator
    {
        if (!empty($this->options)) {
            foreach ($this->options as $option) {

                // 先过滤 interface
                $optInt = $option->interface;

                $checkInterface = isset($interface);
                $validInterface = $this->isValidInterface($optInt, $interface);

                // 需要检查 interface, 但是不一致.
                if ($checkInterface && !$validInterface) {
                    continue;
                }

                // 再过滤 protocal
                $protocalName = $option->protocal;
                if (!is_a($protocal, $protocalName, TRUE)) {
                    continue;
                }

                // 如果校验规则正确, 返回 handler 的配置.
                $handlers = $option->handlers;
                foreach ($handlers as $handlerOption) {
                    $matched = $this->matchProtocalId($protocal->getProtocalId(), $handlerOption->filters);

                    if (!$matched) {
                        continue;
                    }

                    $handler = $handlerOption->handler;

                    // 检查 handler 是否和定义中预期的一致.
                    if (!empty($optInt) && !is_a($handler, $optInt, TRUE)) {
                        $this->logger->error(
                            "protocal option get invalid handler $handler which is not subclass of interface $interface"
                        );
                        continue;
                    }

                    yield $handlerOption;
                }

                // 默认的 handler
                $defaultHandler = $option->getDefaultHandler();
                if (isset($defaultHandler)) {
                    yield $defaultHandler;
                }
            }
        }
    }

    protected function isValidInterface(string $optInt, string $interface = null) : bool
    {
        // interface 校验要求定义的 option 必须是传入的 interface 的子集.
        return  $optInt === $interface
            || (
                !empty($optInt) && is_a($optInt, $interface, true)
            );
    }

    public function matchFirst(Protocal $protocal, string $interface = null) : ? HandlerOption
    {
        foreach ($this->matchEach($protocal, $interface) as $option) {
            return $option;
        }

        return null;
    }

    public function matchProtocalId(string $protocalId, array $rules) : bool
    {
        if (empty($rules)) {
            return true;
        }

        foreach ($rules as $rule) {

            if ($rule === '*') {
                return true;
            }

            // 正则匹配.
            if (StringUtils::isRegexPattern($rule)) {
                return (bool) preg_match($rule, $protocalId);
            }

            // 允许用通配符.
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
