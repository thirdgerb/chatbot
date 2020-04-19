<?php


namespace Commune\Framework\Prototype\Command;


use Commune\Framework\Blueprint\Command\CommandDef;
use Commune\Framework\Blueprint\Command\CommandMsg;
use Commune\Framework\Blueprint\Session;
use Commune\Framework\Blueprint\Session\SessionCmdPipe;
use Commune\Message\Predefined\SystemInts\CommandDescInt;
use Commune\Message\Predefined\SystemInts\CommandNotExistsInt;

/**
 * 帮助命令. 查看当前可用的命令. 或者查看当前命令的选项.
 */
class HelpCmd extends ISessionCmd
{
    const SIGNATURE = 'help
        {commandName? : 命令的名称.比如 /help }
    ';

    const DESCRIPTION = '查看可用指令. 也可以输入 "命令名 -h", 例如"help -h"';

    public function handle(CommandMsg $message, Session $session, SessionCmdPipe $pipe) : void
    {
        if (empty($message['commandName'])) {
            $this->helpPipe($pipe);
        } else {
            $this->helpCommandName($session, $message['commandName'], $pipe);
        }
    }

    public function helpPipe( SessionCmdPipe $pipe) : void
    {
        $mark = $pipe->getCommandMark();
        $messages = [];
        foreach ($pipe->getDescriptions() as $name => $description) {
            $messages[$mark . $name] = $description;
        }

        $available = $this->rangeMessages($messages);
        $this->say(['available' => $available])
            ->info('command.available');
    }

    public function helpCommandName(
        Session $session,
        string $commandName,
        SessionCmdPipe $pipe
    ) : void
    {
        if (!$pipe->hasCommand($commandName)) {
            $this->output(new CommandNotExistsInt($commandName));
        }

        $id = $pipe->getCommandID($commandName);
        $command = $session->getContainer()->make($id);
        $desc = $pipe->getCommandDesc($commandName);
        $this->helpCommand($command->getCommandDefinition(), $desc);
    }

    public function helpCommand(CommandDef $definition, string $desc) : void
    {
        $commandName = $definition->getCommandName();

        // 变量
        $arguments = $definition->getArguments();
        $args = [];
        if (!empty($arguments)) {
            foreach ($arguments as $argument) {
                $args[$argument->getName()] = $argument->getDescription();
            }
        }

        $options = $definition->getOptions();
        $opts = [];
        if (!empty($options)) {

            foreach ($options as $option) {
                $name = $option->getName();
                $shotCut = $option->getShortcut();
                $shotCutStr = $shotCut
                    ?  "-$shotCut,"
                    : '';

                $key = "$shotCutStr--$name";
                $opts[$key] = $option->getDescription();
            }
        }

        $this->output(
            new CommandDescInt(
                $commandName,
                $this->rangeMessages($args),
                $this->rangeMessages($options)
            )
        );
    }

    protected function rangeMessages(array $lines) : string
    {
        $keys = array_keys($lines);

        $maxLength = 0;
        foreach ($keys as $key) {
            $len = strlen($key);
            if ($len > $maxLength) {
                $maxLength = $len;
            }
        }

        $str = '';

        foreach ($lines as $key => $value) {
            $line = '';

            for ($i = 0; $i < $maxLength ; $i ++ ) {
                $line .= $key[$i] ?? ' ';
            }
            $str.="  $line : $value\n";
        }

        return $str;
    }
}