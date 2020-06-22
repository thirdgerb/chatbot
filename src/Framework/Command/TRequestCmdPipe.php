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

use Commune\Blueprint\Framework\Pipes\RequestCmdPipe;
use Commune\Blueprint\Kernel\Protocals\HasInput;
use Commune\Blueprint\Kernel\Protocals\InputRequest;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;
use Commune\Protocals\Intercom\InputMsg;
use Psr\Log\LoggerInterface;
use Illuminate\Console\Parser;
use Commune\Protocals\Comprehension;
use Commune\Ghost\Support\CommandUtils;
use Commune\Container\ContainerContract;
use Commune\Blueprint\Framework\Pipes\RequestCmd;
use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Exceptions\Logic\InvalidClassException;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
trait TRequestCmdPipe
{

    /*---- cached property ----*/

    protected static $commandNames = [];

    protected static $commandDescriptions = [];

    abstract public function getContainer() : ContainerContract;

    abstract public function getLogger() : LoggerInterface;


    public function getInputText(InputMsg $input): ? string
    {
        $message = $input->getMessage();
        if ($message instanceof VerbalMsg) {
            // 区分大小写
            return $message->getText();
        }

        return null;
    }



    /**
     * @param AppRequest $request
     * @param \Closure $next
     * @return AppResponse
     */
    public function tryHandleCommand(AppRequest $request, \Closure $next) : AppResponse
    {

        if (!$request instanceof InputRequest) {
            return $next($request);
        }


        $this->init();
        $input = $request->getInput();
        $comprehension = $request->getComprehension();

        // 处理过
        $command = $comprehension->command;

        // 不存在的话, 尝试检查一下.
        if (!$command->hasCmdStr()) {

            $text = $this->getInputText($input);

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
                Comprehension::TYPE_COMMAND,
                static::class,
                $success
            );
        }

        $cmdStr = $command->getCmdStr();
        return isset($cmdStr)
            ? $this->runMatchedCommand($request, $cmdStr, $next)
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

    public function runMatchedCommand(
        AppRequest $request,
        string $cmdStr,
        \Closure $next
    ) : AppResponse
    {
        // 匹配原理很简单, 就看命令是否命中了.
        foreach (self::getCommandsMap() as $cmdName => $clazz) {
            if (CommandUtils::matchCommandName($cmdStr, $cmdName)) {
                // 命中了的话就执行.
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
        $response = $command->handleSession($request, $this, $cmdStr);

        return $response;
    }




}