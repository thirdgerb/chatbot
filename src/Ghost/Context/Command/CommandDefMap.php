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

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\Support\CommandUtils;
use Commune\Message\Host\SystemInt\CommandErrorInt;


/**
 * 语境相关的命令定义集合.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CommandDefMap
{
    /**
     * @var self[]
     */
    private static $maps = [];

    /**
     * @var AContextCmdDef[]
     */
    private static $defMaps = [];

    /**
     * @var string
     */
    protected $commandMark;

    /**
     * @var string[]
     */
    protected $commands;


    /**
     * @var AContextCmdDef[]
     */
    protected $defs;

    /**
     * CommandDefMap constructor.
     * @param string $commandMark
     * @param string[] $commands
     */
    public function __construct(string $commandMark, array $commands)
    {
        $this->commandMark = $commandMark;
        $this->commands = $commands;
    }


    /**
     * 获取一个 Map
     * @param ContextDef $def
     * @return CommandDefMap|null
     */
    public static function findMap(ContextDef $def) : ? self
    {
        $name = $def->getName();
        if (isset(self::$maps[$name])) {
            return self::$maps[$name];
        }

        $strategy = $def->getStrategy();
        $commands = $strategy->commands;
        if (empty($commands)) {
            return null;
        }

        $instance = new self($strategy->commandMark, $commands);
        return self::$maps[$name] = $instance;
    }


    /**
     * 在当前 map 下尝试运行命令.
     * @param Dialog $dialog
     * @param string $text
     * @return Operator|null
     */
    public function runCommand(Dialog $dialog, string $text) : ? Operator
    {
        $text = trim($text);

        if (empty($text)) {
            return null;
        }

        $cmdStr = CommandUtils::getCommandStr($text, $this->commandMark);
        if (empty($cmdStr)) {
            return null;
        }

        $cmdName = CommandUtils::getCommandNameStr($cmdStr);
        if (empty($cmdName)) {
            return null;
        }

        $map = $this->getContextCmdDefMap();

        if (!isset($map[$cmdName])) {
            return null;
        }

        $def = $map[$cmdName];
        $commandMessage = $def->getCommandDef()->parseCommandMessage($cmdStr);

        if ($commandMessage->isCorrect()) {
            return $def->handle($dialog, $commandMessage);
        }


        $errorBag = $commandMessage->getErrors();

        $error = '';
        foreach ($errorBag as $type => $errors) {
            $error .= PHP_EOL . $type;
            $msg = is_array($errors) ? implode(PHP_EOL.'  ', $errors) : $errors;
            $error .= PHP_EOL . $msg;
        }

        return $dialog
            ->send()
            ->message(
                CommandErrorInt::instance(
                    $cmdName,
                    $error
                )
            )
            ->over()
            ->dumb();
    }


    /**
     * 获取 map 下所有的 def,  命令名 => AContextCmdDef
     * @return AContextCmdDef[]
     */
    public function getContextCmdDefMap() : array
    {
        if (isset($this->defs)) {
            return $this->defs;
        }

        $defs = [];

        foreach ($this->commands as $commandClassName) {

            if (!array_key_exists($commandClassName, self::$defMaps)) {
                self::$defMaps[$commandClassName] = new $commandClassName;
            }
            $defs[$commandClassName] = self::$defMaps[$commandClassName];
        }


        // help
        $helpDef = self::$defMaps[HelpCmdDef::class]
            ?? self::$defMaps[HelpCmdDef::class] = new HelpCmdDef();
        $defs[HelpCmdDef::class] = $helpDef;

        // 赋值给当前实例.
        $this->defs = [];
        foreach ($defs as $def) {
            $name = $def->getCommandDef()->getCommandName();
            $this->defs[$name] = $def;
        }
        return $this->defs;
    }

    public function __destruct()
    {
        unset(
            $this->defs,
            $this->commands
        );
    }
}