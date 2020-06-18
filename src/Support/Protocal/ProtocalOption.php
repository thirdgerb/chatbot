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
use Commune\Support\Utils\TypeUtils;


/**
 * 协议 handler 的过滤配置.
 * 挑选协议的原理是: group / protocal / protocalId
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $protocal              协议的名称.
 * @property-read string $interface             Handler 的 interface, 同时也可作为分组. 允许为空.
 * @property-read HandlerOption[] $handlers     定义的 handlers.
 * @property-read string|null $default
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

    public function getId(): string
    {
        return $this->_id ?? $this->_id = $this->getHash();
    }

    public static function relations(): array
    {
        return [
            'handlers[]' => HandlerOption::class,
        ];
    }

    public static function validate(array $data): ? string /* errorMsg */
    {
        return TypeUtils::requireFields($data, ['protocal', 'handlers'])
            ?? parent::validate($data);
    }


    public function __set_protocal(string $name, string $val)
    {
        $this->_data[$name] = AliasesForProtocal::getAliasOfOrigin($val);
    }

    public function __get_protocal(string $name) : string
    {
        return AliasesForProtocal::getOriginFromAlias($this->_data[$name] ?? '');
    }

    public function __set_interface(string $name, string $val) : void
    {
        $this->_data[$name] = AliasesForProtocal::getAliasOfOrigin($val);
    }


    public function __get_interface(string $name) : string
    {
        return AliasesForProtocal::getOriginFromAlias($this->_data[$name] ?? '');
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