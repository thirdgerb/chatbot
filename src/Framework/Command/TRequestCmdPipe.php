<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Command;

use Commune\Blueprint\Framework\Pipes\RequestCmd;
use Commune\Blueprint\Framework\Pipes\RequestCmdPipe;
use Commune\Blueprint\Framework\Request\AppRequest;
use Commune\Blueprint\Framework\Request\AppResponse;
use Commune\Blueprint\Exceptions\Logic\InvalidClassException;
use Commune\Container\ContainerContract;
use Commune\Ghost\Support\CommandUtils;
use Commune\Protocals\Abstracted\Cmd;
use Illuminate\Console\Parser;
use Psr\Log\LoggerInterface;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
trait TRequestCmdPipe
{

    /*---- cached property ----*/

    protected static $commandNames = [];

    protected static $commandDescriptions = [];

    abstract public function getContainer() : ContainerContract;

    abstract public function getLogger() : LoggerInterface;

    abstract public function getCommandMark() : string;

    abstract public function getInputText(AppRequest $request) : ? string;

    /**
     * @return string[]
     *  [
     *    commandName => commandClassName
     *  ]
     */
    abstract public function getCommands(): array;


    public function tryHandleCommand(AppRequest $request, \Closure $next) : AppResponse
    {
        $this->init();

        $input = $request->getInput();
        $comprehension = $input->comprehension;

        // 处理过
        $handled = $comprehension->isHandled(Cmd::class);
        $command = $comprehension->command;

        // 不存在的话, 尝试检查一下.
        if (!$handled) {

            $text = $this->getInputText($request);

            $cmdStr = null;
            if (!empty($text)) {
                $mark = $this->getCommandMark();
                $cmdStr = CommandUtils::getCommandStr($text, $mark);
            }

            $success = false;
            // 不是命令的话, 跳走.
            if (isset($cmdStr)) {
                $command->setCmdStr($cmdStr);
                $success = true;
            }

            $comprehension->handled(
                Cmd::class,
                static::class,
                $success
            );
        }

        $cmdStr = $command->getCmdStr();

        return isset($cmdStr)
            ? $this->matchCommand($request, $cmdStr, $next)
            : $next($request);
    }


    public static function registerCommandName(string $commandName) : void
    {
        // 注册 command
        if (is_a($commandName, RequestCmd::class, TRUE)) {
            $desc = constant("$commandName::DESCRIPTION");
            list($name, $arguments, $options) = Parser::parse(constant("$commandName::SIGNATURE"));
            self::$commandNames[static::class][$name] = $commandName;
            self::$commandDescriptions[static::class][$name] = $desc;
            return;
            // 注册 command intent
        }

        throw new InvalidClassException(
            RequestCmd::class,
            $commandName
        );
    }

    protected function init() : void
    {
        $name = static::class;
        // reactor 内只注册一次.
        if (!isset(self::$commandNames[$name])) {
            foreach ($this->getCommands() as $commandName) {
                $this->registerCommandName($commandName);
            }
        }
    }

    protected function getCommandsMap() : array
    {
        $this->init();
        $name = static::class;
        return self::$commandNames[$name] ?? [];
    }

    public function matchCommand(
        AppRequest $request,
        string $cmdStr,
        \Closure $next
    ) : AppResponse
    {
        // 匹配原理很简单, 就看命令是否命中了.
        foreach (self::getCommandsMap() as $cmdName => $clazz) {
            if (CommandUtils::matchCommandName($cmdStr, $cmdName)) {

                $res = $this->runCommand($request, $cmdName, $cmdStr)
                    ?? $next($request);

                return $res;
            }
        }

        return $next($request);
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

    public function getDescriptions() : array
    {
        return self::$commandDescriptions[static::class];
    }

    public function runCommand(
        AppRequest $request,
        string $commandName,
        string $cmdStr
    ) : ? AppResponse
    {
        if (empty($commandName)) {
            throw new \InvalidArgumentException(
                static::class
                . ' commandName is empty'
            );
        }

        // 稍微记录个日志.
        $this->getLogger()->info(
            static::class
            . ' run command '
            . $commandName
        );

        /**
         * @var RequestCmd $command
         */
        $commandID = $this->getCommandID($commandName);
        // session command
        $command = $this->getContainer()->make($commandID);
        return $command->handleSession($request, $this, $cmdStr);
    }




}