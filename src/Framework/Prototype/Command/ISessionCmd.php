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

use Commune\Framework\Blueprint\Command\CommandDef;
use Commune\Framework\Blueprint\Command\CommandMsg;
use Commune\Framework\Blueprint\Session\Session;
use Commune\Framework\Blueprint\Session\SessionCmd;
use Commune\Framework\Blueprint\Session\SessionCmdPipe;
use Commune\Message\Blueprint\Message;
use Commune\Message\Prototype\IIntentMsg;
use Commune\Message\Prototype\Intents\CommandInvalidInt;
use Psr\Log\LoggerTrait;
use Symfony\Component\Console\Input\InputOption;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ISessionCmd implements SessionCmd
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

    /**
     * 是否让 Session 保存状态变更.
     * true 的话, Session 不对本轮对话进行任何记录.
     *
     * @var bool
     */
    protected $silent = true;


    /*------ 缓存类属性 ------*/

    protected static $definitions = [];

    /**
     * @var Session
     */
    protected $session;

    abstract public function handle(CommandMsg $message, Session $session, SessionCmdPipe $pipe) : void;

    public function withSession(Session $session)
    {
        $this->session = $session;
        return $this;
    }

    public function handleSession(Session $session, SessionCmdPipe $pipe, string $cmdText) : Session
    {
        $this->withSession($session);

        $command = $this;
        $commandMsg = $this
            ->getCommandDef()
            ->toCommandMessage($cmdText);

        // 跳转运行 help
        if ($commandMsg['--help'] ) {

            if ($this instanceof HelpCmd) {
                $helper = $this;
            } else {
                /**
                 * @var HelpCmd $helper
                 */
                $helper = $session->getContainer()->make(HelpCmd::class);
                $helper->withSession($session);
            }


            $helper->helpCommand(
                $this->getCommandDef(),
                $this->getDescription()
            );
            return $session;
        }

        if ($commandMsg->isCorrect()) {
            $command->handle($commandMsg, $session, $pipe);
            return $session;
        }

        $errorBag = $commandMsg->getErrors();
        $command->sendError($errorBag);
        return $session;
    }

    public function sendError(array $errorBag) : void
    {
        $text = '';
        foreach ($errorBag as $type => $errors) {
            $text .= PHP_EOL . $type;
            $msg = is_array($errors) ? implode(PHP_EOL.'  ', $errors) : $errors;
            $text .= PHP_EOL . $msg;
        }

        $this->session->output(new CommandInvalidInt(
            $this->getCommandName(),
            $text
        ));
    }


    public function getDescription() : string
    {
        return static::DESCRIPTION;
    }

    public function getCommandName() : string
    {
        $definition = $this->getCommandDef();
        return $definition->getCommandName();
    }

    public function getCommandDef() : CommandDef
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
        $this->session->output(
            new IIntentMsg($message, $context, $level)
        );
    }

    public function output(Message $message) : void
    {
        $this->session->output($message);
    }


}