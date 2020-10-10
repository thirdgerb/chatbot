<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Protocol;

use Commune\Support\Utils\StringUtils;
use Psr\Log\LoggerInterface;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ProtocolMatcher
{

    /**
     * @var ProtocolOption[]
     */
    protected $options = [];

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var string
     */
    protected $wildcardPattern;

    /**
     * ProtocolMatcher constructor.
     * @param LoggerInterface $logger
     * @param array $options
     * @param string $wildcardPattern
     */
    public function __construct(
        LoggerInterface $logger,
        array $options = [],
        string $wildcardPattern = '[\.\w]+'
    )
    {
        $this->wildcardPattern = $wildcardPattern;
        $this->logger = $logger;
        foreach ($options as $option) {
            $this->addOption($option);
        }
    }

    public function getOptions() : array
    {
        return $this->options;
    }

    public function addOption(ProtocolOption $option) : void
    {
        $id = $option->getId();
        $this->options[$id] = $option;
    }

    /**
     * @param Protocol $Protocol
     * @param string|null $interface
     * @return \Generator|HandlerOption[]
     */
    public function matchEach(Protocol $Protocol, string $interface = null) : \Generator
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

                // 再过滤 Protocol
                $ProtocolName = $option->Protocol;
                if (!is_a($Protocol, $ProtocolName, TRUE)) {
                    continue;
                }

                // 如果校验规则正确, 返回 handler 的配置.
                $handlers = $option->handlers;
                foreach ($handlers as $handlerOption) {
                    $ProtocolId = $Protocol->getProtocolId();
                    $filters = $handlerOption->filters;
                    $matched = $this->matchProtocolId($ProtocolId, $filters);

                    if (!$matched) {
                        continue;
                    }

                    $handler = $handlerOption->handler;

                    // 检查 handler 是否和定义中预期的一致.
                    if (!empty($optInt) && !is_a($handler, $optInt, TRUE)) {
                        $this->logger->error(
                            "Protocol option get invalid handler $handler which is not subclass of interface $interface"
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

    public function matchFirst(Protocol $Protocol, string $interface = null) : ? HandlerOption
    {
        foreach ($this->matchEach($Protocol, $interface) as $option) {
            return $option;
        }

        return null;
    }

    public function matchProtocolId(string $ProtocolId, array $rules) : bool
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
                return (bool) preg_match($rule, $ProtocolId);
            }

            // 允许用通配符.
            $matched = StringUtils::isWildcardPattern($rule)
                // 只匹配字母的情况. 暂时不做更复杂的匹配逻辑.
                ? StringUtils::wildcardMatch($rule, $ProtocolId, $this->wildcardPattern)
                : $rule === $ProtocolId;

            if ($matched) {
                return true;
            }
        }

        return false;
    }
}
