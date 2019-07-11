<?php


namespace Commune\Chatbot\OOHost\Command;


use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Utils\CommandUtils;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionPipe;
use Illuminate\Console\Parser;

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
        $repo = IntentRegistrar::getIns();
        // 注册 command
        if (is_a($commandName, SessionCommand::class, TRUE)) {
            $desc = constant("$commandName::DESCRIPTION");
            list($name, $arguments, $options) = Parser::parse(constant("$commandName::SIGNATURE"));
            self::$commandNames[static::class][$name] = $commandName;
            self::$commandDescriptions[static::class][$name] = $desc;
            return;
        // 注册 command intent
        }


        // 如果有可作为命令的 intent (signature 不为空 ) 存在
        if ($repo->hasCommandIntent($commandName)) {
            $matcher = $repo->getMatcher($commandName);
            $command = $matcher->getCommand();
            $name = $command->getCommandName();
            self::$commandNames[static::class][$name] = $commandName;
            self::$commandDescriptions[static::class][$name] = $repo
                ->get($commandName)
                ->getDesc();
            return;
        }

        throw new ConfigureException(
            static::class
            . ' can only register command intent, or clazz instance of '
            . SessionCommand::class
            . ', ' . $commandName . ' given'
        );
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
        $commandID = $this->getCommandID($commandName);
        // session command
        $command = $this->makeCommand($session, $commandID);
        return $command->handleSession($session, $this, $cmdStr);
    }

    public function makeCommand(Session $session, string $commandID) : SessionCommand
    {
        if (is_a($commandID, SessionCommand::class, TRUE)) {
            return $session->conversation->make($commandID);
        }

        $repo = IntentRegistrar::getIns();
        if ($repo->hasCommandIntent($commandID)) {
            return new IntentCmd($commandID);
        }

        throw new ConfigureException(
                static::class
                . ' only make '.SessionCommand::class
                . ', or command intent, '.$commandID .' given'
            );
    }


    public function getCommandMark() : string
    {
        return $this->commandMark;
    }

}