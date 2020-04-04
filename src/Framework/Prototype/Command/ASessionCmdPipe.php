<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Prototype\Command;

use Commune\Framework\Blueprint\Session\Session;
use Commune\Framework\Blueprint\Session\SessionCmd;
use Commune\Framework\Blueprint\Session\SessionCmdPipe;
use Commune\Framework\Exceptions\InvalidClassException;
use Illuminate\Console\Parser;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ASessionCmdPipe implements SessionCmdPipe
{
    /*---- cached property ----*/

    protected static $commandNames = [];

    protected static $commandDescriptions = [];

    abstract public function getCommandMark() : string;

    /**
     * @return string[]
     *  [
     *    commandName => commandClassName
     *  ]
     */
    abstract public static function getCommandNames(): array;


    protected function init(Session $session) : void
    {
        $name = static::class;
        // reactor 内只注册一次.
        if (!isset(self::$commandNames[$name])) {
            foreach ($this->getCommands() as $commandName) {
                $this->registerCommandName($commandName);
            }
        }
    }

    public static function registerCommandName(string $commandName) : void
    {
        // 注册 command
        if (is_a($commandName, SessionCmd::class, TRUE)) {
            $desc = constant("$commandName::DESCRIPTION");
            list($name, $arguments, $options) = Parser::parse(constant("$commandName::SIGNATURE"));
            self::$commandNames[static::class][$name] = $commandName;
            self::$commandDescriptions[static::class][$name] = $desc;
            return;
            // 注册 command intent
        }

        throw new InvalidClassException(
            SessionCmd::class,
            $commandName
        );
    }



    public function sync(Session $session, callable $next) : Session
    {
        $this->init($session);

        $text = $session->getGhostInput()->getTrimmedText();

        if (empty($text)) {
            return $next($session);
        }

        $mark = $this->getCommandMark();
        $cmdStr = CommandUtils::getCommandStr($text, $mark);

        // 不是命令的话, 跳走.
        if (!isset($cmdStr)) {
            return $next($session);
        }

        return $this->matchCommand($cmdStr, $session, $next);
    }

    public function matchCommand(string $cmdStr, Session $session, \Closure $next) : Session
    {
        // 匹配原理很简单, 就看命令是否命中了.
        $commands = $this->getCommands();
        foreach ($commands as $name => $clazz) {
            if (CommandUtils::matchCommandName($cmdStr, $name)) {
                return $this->runCommand($session, $name, $cmdStr);
            }
        }

        return $next($session);
    }

    public function hasCommand(string $name) : bool
    {
        return isset(self::$commandNames[static::class][$name]);
    }

    public function getCommandID(string $name) : string
    {
        return self::$commandNames[static::class][$name] ?? '';
    }

    public function getCommandDesc(string $name) : string
    {
        return self::$commandDescriptions[static::class][$name];
    }

    public function getCommands() : array
    {
        return self::$commandNames[static::class];
    }

    public function getDescriptions() : array
    {
        return self::$commandDescriptions[static::class];
    }

    public function runCommand(Session $session, string $commandName, string $cmdStr) : Session
    {
        if (empty($commandName)) {
            throw new \InvalidArgumentException(
                static::class
                . ' commandName is empty'
            );
        }

        // 稍微记录个日志.
        $session->getLogger()->info(
            static::class
            . ' run command '
            . $commandName
        );

        /**
         * @var SessionCmd $command
         */
        $commandID = $this->getCommandID($commandName);
        // session command
        $command = $session->getContainer()->make($commandID);
        return $command->handleSession($session, $this, $cmdStr);
    }





}