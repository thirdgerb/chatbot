<?php


namespace Commune\Chatbot\OOHost\Command;


use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Speech;
use Commune\Chatbot\OOHost\Session\Session;

use Commune\Chatbot\Blueprint\Message\Command\CmdMessage;

use Commune\Chatbot\Framework\Utils\CommandUtils;
use Symfony\Component\Console\Input\InputOption;


abstract class SessionCommand
{
    const SIGNATURE = 'test';

    const DESCRIPTION = '';

    protected static $definitions = [];

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Dialog
     */
    protected $dialog;

    /**
     * @var bool
     */
    protected $sneak = true;

    abstract public function handle(CmdMessage $message, Session $session, SessionCommandPipe $pipe) : void;

    /**
     * 调用 dialog 的对话模块.
     *
     * @param array $slots
     * @return Speech
     */
    public function say(array $slots = []) : Speech
    {
        return $this->dialog->say($slots);
    }

    public function withSession(Session $session) : self
    {
        $this->session = $session;
        $this->dialog = $session->dialog;
        if ($this->sneak) {
            $this->session->beSneak();
        }
        return $this;
    }

    public function handleSession(Session $session, SessionCommandPipe $pipe) : void
    {
        $this->withSession($session);
        $message = $session->incomingMessage->message;

        // cmdText 不同于 intent, 不一定使用 user command mark
        $cmdText = CommandUtils::getCommandStr(
            $message->getTrimmedText(),
            $pipe->getCommandMark()
        );

        $command = $this;
        $commandMsg = static::getCommandDefinition()
            ->toCommandMessage($cmdText, $message);

        // 跳转运行 help
        if ($commandMsg['--help'] && !$this instanceof Help) {
            /**
             * @var Help $helper
             */
            $helper = $pipe->makeCommand($session, Help::class);
            $helper->withSession($session);

            $helper->helpCommandClazz(
                static::class,
                $this->getDescription()
            );
            return;
        }

        if ($commandMsg->isCorrect()) {
            $command->handle($commandMsg, $session, $pipe);
            return;
        }

        $errorBag = $commandMsg->getErrors();
        $command->sendError($errorBag);
        return;
    }

    public function sendError(array $errorBag) : void
    {
        $text = '命令'. $this->getCommandName() . ' 参数错误: ';
        foreach ($errorBag as $type => $errors) {
            $text .= PHP_EOL . $type;
            $msg = is_array($errors) ? implode(PHP_EOL.'  ', $errors) : $errors;
            $text .= PHP_EOL . $msg;
        }

        $this->say()->warning($text);
    }


    public static function getDescription() : string
    {
        return static::DESCRIPTION;
    }

    public static function getCommandName() : string
    {
        $definition = self::getCommandDefinition();
        return $definition->getCommandName();
    }

    public static function getCommandDefinition() : CommandDefinition
    {
        $name = static::class;
        if (isset(self::$definitions[$name])) {
            return self::$definitions[$name];
        }

        $definition = CommandDefinition::makeBySignature(static::SIGNATURE);

        $definition->addOption(new InputOption(
            'help',
            'h',
            null,
            'help to see command detail'
        ));

        self::$definitions[$name] = $definition;
        return $definition;
    }


    public function __destruct()
    {
        //检查是否销毁.
        if (CHATBOT_DEBUG) $this->session->logger->debug(
            __CLASS__. '::' . __FUNCTION__
        );
    }

}