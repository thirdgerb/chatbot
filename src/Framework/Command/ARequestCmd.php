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

use Psr\Log\LoggerTrait;
use Commune\Protocals\HostMsg;
use Commune\Message\Host\IIntentMsg;
use Commune\Blueprint\Framework\Command\CommandDef;
use Commune\Blueprint\Framework\Pipes\RequestCmdPipe;
use Commune\Blueprint\Framework\Request\AppRequest;
use Commune\Blueprint\Framework\Request\AppResponse;
use Commune\Message\Host\SystemInt\CommandErrorInt;
use Commune\Blueprint\Framework\Pipes\RequestCmd;
use Symfony\Component\Console\Input\InputOption;
use Commune\Blueprint\Framework\Command\CommandMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ARequestCmd implements RequestCmd
{
    use LoggerTrait;


    /**
     * 命令的规则定义. 使用了 Laravel 的命令 parser
     *
     * 具体的定义方法 @see https://laravel.com/docs/6.x/artisan#defining-input-expectations
     */
    const SIGNATURE = 'test';

    /**
     * 命令的简介, 用于介绍命令的功能.
     */
    const DESCRIPTION = '';


    /*------ 缓存类属性 ------*/

    protected static $definitions = [];

    /**
     * @var HostMsg[]
     */
    protected $outputs = [];

    abstract protected function getHelpCmd() : AbsHelpCmd;

    abstract protected function handle(CommandMsg $command, RequestCmdPipe $pipe) : void;

    abstract protected function response(AppRequest $request, array $messages) : ? AppResponse;

    abstract protected function checkRequest(AppRequest $request) : void;

    public function handleSession(
        AppRequest $request,
        RequestCmdPipe $pipe,
        string $cmdText
    ): ? AppResponse
    {

        $this->checkRequest($request);

        $command = $this;
        $commandMsg = $this
            ->getCommandDef()
            ->parseCommandMessage($cmdText);

        // 跳转运行 help
        if ($commandMsg['--help'] ) {

            if ($this instanceof AbsHelpCmd) {
                $helper = $this;
            } else {
                /**
                 * @var AbsHelpCmd $helper
                 */
                $helper = $this->getHelpCmd();
            }

            return $helper->descCommand(
                $request,
                $this->getCommandDef(),
                $this->getDescription()
            );
        }

        if ($commandMsg->isCorrect()) {
            $command->handle($commandMsg, $pipe);
        } else {
            $errorBag = $commandMsg->getErrors();
            $command->sendError($errorBag);
        }

        return $this->response($request, $this->outputs);
    }

    public function sendError(array $errorBag): void
    {
        $text = '';
        foreach ($errorBag as $type => $errors) {
            $text .= PHP_EOL . $type;
            $msg = is_array($errors) ? implode(PHP_EOL.'  ', $errors) : $errors;
            $text .= PHP_EOL . $msg;
        }

        $this->output(new CommandErrorInt(
            $this->getCommandName(),
            $text
        ));
    }

    public function output(HostMsg $message): void
    {
        $this->outputs[] = $message;
    }

    public function getDescription(): string
    {
        return static::DESCRIPTION;
    }

    public static function getCommandName(): string
    {
        $definition = static::getCommandDef();
        return $definition->getCommandName();
    }

    public static function getCommandDef(): CommandDef
    {
        $name = static::class;
        if (isset(self::$definitions[$name])) {
            return self::$definitions[$name];
        }

        $definition = ICommandDef::makeBySignature(static::SIGNATURE);

        $definition->addOption(new InputOption(
            'help',
            'h',
            null,
            '查看命令的参数与选项'
        ));

        self::$definitions[$name] = $definition;
        return $definition;
    }

    public function log($level, $message, array $context = array())
    {
        $this->output(new IIntentMsg(
            $message,
            $context,
            $level
        ));
    }

    public function __destruct()
    {
        $this->outputs = [];
    }

}