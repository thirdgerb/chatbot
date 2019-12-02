<?php


namespace Commune\Chatbot\OOHost\Command;


use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\DialogSpeech;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\Blueprint\Message\Transformed\CommandMsg;
use Symfony\Component\Console\Input\InputOption;


/**
 * 默认的 session command
 * 会自动依赖注入
 *
 * 注意command 不应该是单例. 它会持有session
 */
abstract class SessionCommand
{
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
    protected $sneak = true;


    /*------ 缓存类属性 ------*/

    protected static $definitions = [];

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Dialog
     */
    protected $dialog;

    abstract public function handle(CommandMsg $message, Session $session, SessionCommandPipe $pipe) : void;

    /**
     * 调用 dialog 的对话模块.
     *
     * @param array $slots
     * @return DialogSpeech
     */
    public function say(array $slots = []) : DialogSpeech
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

    public function handleSession(Session $session, SessionCommandPipe $pipe, string $cmdText) : Session
    {
        $this->withSession($session);
        $message = $session->incomingMessage->message;

        $command = $this;
        $commandMsg = $this->getCommandDefinition()
            ->toCommandMessage($cmdText, $message);

        // 跳转运行 help
        if ($commandMsg['--help'] ) {

            if ($this instanceof HelpCmd) {
                $helper = $this;
            } else {
                /**
                 * @var HelpCmd $helper
                 */
                $helper = $pipe->makeCommand($session, HelpCmd::class);
                $helper->withSession($session);
            }


            $helper->helpCommand(
                $this->getCommandDefinition(),
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
        $text = '命令'. $this->getCommandName() . ' 参数错误: ';
        foreach ($errorBag as $type => $errors) {
            $text .= PHP_EOL . $type;
            $msg = is_array($errors) ? implode(PHP_EOL.'  ', $errors) : $errors;
            $text .= PHP_EOL . $msg;
        }

        $this->say()->warning($text);
    }


    public function getDescription() : string
    {
        return static::DESCRIPTION;
    }

    public function getCommandName() : string
    {
        $definition = $this->getCommandDefinition();
        return $definition->getCommandName();
    }

    public function getCommandDefinition() : CommandDefinition
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
            '查看命令的参数与选项'
        ));

        self::$definitions[$name] = $definition;
        return $definition;
    }

}