<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context\Command;

use Commune\Blueprint\Framework\Command\CommandDef;
use Commune\Blueprint\Framework\Command\CommandMsg;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Framework\Command\ICommandDef;
use Commune\Support\Struct\AStruct;


/**
 * 用于定义一个上下文相关的命令.
 * 命令在许多场景下, 是远比自然语言精确得多的表达方式, 在对话式运维中尤其有用.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $signature
 * 命令的定义, 使用 laravel 的风格, 用字符串定义命令.
 * @see \Commune\Framework\Command\Parser
 *
 * @property-read string $desc
 * 命令的简介, 展示在 help 命令下
 */
abstract class AContextCmdDef extends AStruct
{
    /**
     * @var CommandDef[]
     */
    private static $_defs = [];

    /**
     * 纯定义类, 因此禁止重构.
     * AContextCmdDef constructor.
     */
    final public function __construct()
    {
        parent::__construct([]);
    }


    public static function stub(): array
    {
        return [
            'desc' => '',
            'signature' => '',
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public function getDescription() : string
    {
        return $this->desc;
    }

    final public function getCommandDef() : CommandDef
    {
        $name = static::class;
        if (isset(self::$_defs[$name])) {
            return self::$_defs[$name];
        }

        $def = ICommandDef::makeBySignature($this->signature);
        return self::$_defs[$name] = $def;
    }


    /**
     * 运行命令, 没有使用依赖注入, 因为 Dialog 自带 ioc Dialog::container()->make()
     * 本来打算使用一个 callable 来实现依赖注入, 但 callable 性能开销较大, 而大部分场景并不会用到.
     *
     * @param Dialog $dialog
     * @param CommandMsg $message
     * @return Operator|null
     */
    abstract public function handle(
        Dialog $dialog,
        CommandMsg $message
    ) : ? Operator;
}