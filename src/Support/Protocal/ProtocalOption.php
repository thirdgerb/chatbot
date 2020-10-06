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

use Commune\Support\Option\AbsOption;


/**
 * 协议 handler 的过滤配置.
 * 挑选协议的原理是: group / protocal / protocalId
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $protocal              协议的名称.
 * @property-read string $interface             Handler 的 interface, 同时也可作为分组. 允许为空.
 * @property-read HandlerOption[] $handlers     定义的 handlers.
 * @property-read string|null $default          Handler 的类名. 不为null 时会提供默认的 handler
 */
class ProtocalOption extends AbsOption
{
    protected $_id;

    protected $_default;

    public static function stub(): array
    {
        return [
            'protocal' => '',
            'interface' => '',
            'handlers' => [
            ],
            'default' => null,
        ];
    }

    /**
     * 添加新的 Handler
     * @param HandlerOption $handlerOption
     */
    public function pushHandler(HandlerOption $handlerOption) : void
    {
        $this->_data['handlers'][] = $handlerOption;
    }

    public static function relations(): array
    {
        return [
            'handlers[]' => HandlerOption::class,
        ];
    }


    public function getId(): string
    {
        return $this->_id ?? $this->_id = $this->getHash();
    }

   public function getDefaultHandler() : ? HandlerOption
    {
        if (isset($this->_default)) {
            return $this->_default;
        }

        $default = $this->default;
        if (empty($default)) {
            return null;
        }
        return $this->_default = new HandlerOption([
            'handler' => $default,
        ]);
    }
}