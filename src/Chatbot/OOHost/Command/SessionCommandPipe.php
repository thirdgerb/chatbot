<?php


namespace Commune\Chatbot\OOHost\Command;


use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Utils\CommandUtils;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionPipe;

/**
 *
 * 在Session 场景中可用的命令管道.
 * 用这个管道直接可以实现命令式的对话响应.
 *
 * Class SessionCommandPipe
 * @package Commune\Chatbot\Host\Command
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SessionCommandPipe implements SessionPipe
{
    /*---- config ----*/

    // 命令的名称.
    protected $commands = [];

    // 定义一个 command mark
    protected $commandMark = '/';


    /*---- cached property ----*/

    protected static $commandNames = [];

    protected static $commandDescriptions = [];

    public function __construct()
    {
        $name = static::class;
        // reactor 内只注册一次.
        if (!isset(self::$commandNames[$name])) {
            foreach ($this->commands as $commandName) {
                $this->registerCommandName($commandName);
            }
        }

    }

    protected function registerCommandName(string $commandName) : void
    {
        if (!is_a($commandName, SessionCommand::class, TRUE)) {
            throw new ConfigureException(
                static::class
                . ' can only register clazz instance of '
                . SessionCommand::class
                . ', ' . $commandName . ' given'
            );
        }

        $func = "$commandName::getCommandName";
        $desc = "$commandName::getDescription";
        $name = $func();
        self::$commandNames[static::class][$name] = $commandName;
        self::$commandDescriptions[static::class][$name] = $desc();
    }


    /**
     * @param Session $session
     * @param \Closure $next
     * @return Session
     */
    public function handle(Session $session, \Closure $next) : Session
    {
        $message = $session->incomingMessage->message;

        if (!$message instanceof VerboseMsg) {
            return $next($session);
        }

        $text = $message->getTrimmedText();
        $mark = $this->getCommandMark();
        $cmdStr = CommandUtils::getCommandStr($text, $mark);


        // 不是命令的话, 跳走.
        if (!isset($cmdStr)) {
            return $next($session);
        }

        // 匹配原理很简单, 就看命令是否命中了.
        $commands = $this->getCommands();
        foreach ($commands as $name => $clazz) {
            if (CommandUtils::matchCommandName($cmdStr, $name)) {
                $this->runCommand($session, $name);
                return $session;
            }
        }

        return $next($session);
    }

    public function hasCommand(string $name) : bool
    {
        return isset(self::$commandNames[static::class][$name]);
    }

    public function getCommandClazz(string $name) : string
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

    public function runCommand(Session $session, string $commandName) : void
    {
        if (empty($commandName)) {
            throw new \InvalidArgumentException(
                static::class
                . ' commandName is empty'
            );
        }

        // 稍微记录个日志.
        if (CHATBOT_DEBUG) {
            $session->logger->debug(
                static::class
                . ' run command '
                . $commandName
            );
        }

        /**
         * @var SessionCommand $command
         */
        $commandClazz = $this->getCommandClazz($commandName);
        $command = $this->makeCommand($session, $commandClazz);
        $command->handleSession($session, $this);
    }

    public function makeCommand(Session $session, string $commandClazz) : SessionCommand
    {
        if (!is_a($commandClazz, SessionCommand::class, TRUE)) {
            throw new ConfigureException(
                static::class
                . ' only make '.SessionCommand::class
                . ', '.$commandClazz .' given'
            );
        }

        return $session->conversation->make($commandClazz);
    }


    public function getCommandMark() : string
    {
        return $this->commandMark;
    }


}