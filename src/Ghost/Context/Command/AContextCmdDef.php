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
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $desc
 * @property-read string $signature
 */
abstract class AContextCmdDef extends AStruct
{
    /**
     * @var CommandDef[]
     */
    private static $_defs = [];


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


    abstract public function handle(
        Dialog $dialog,
        CommandMsg $message
    ) : ? Operator;
}