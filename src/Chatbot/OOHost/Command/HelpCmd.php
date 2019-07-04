<?php


namespace Commune\Chatbot\OOHost\Command;


use Commune\Chatbot\Blueprint\Message\Command\CmdMessage;
use Commune\Chatbot\OOHost\Session\Session;

class HelpCmd extends SessionCommand
{
    const SIGNATURE = 'help
        {commandName? : 命令的名称.比如 /help }
    ';

    const DESCRIPTION = '查看可用指令介绍';

    public function handle(CmdMessage $message, Session $session, SessionCommandPipe $pipe): void
    {
        if (empty($message['commandName'])) {
            $this->helpPipe($pipe);
        } else {
            $this->helpCommandName($session, $message['commandName'], $pipe);
        }
    }

    public function helpPipe( SessionCommandPipe $pipe) : void
    {
        $available = '';
        foreach ($pipe->getDescriptions() as $name => $description) {
            $available .= "  $name\t:\t$description" .PHP_EOL;
        }

        $this->say(['%available%' => $available])
            ->info('command.available');
    }

    public function helpCommandName(
        Session $session,
        string $commandName,
        SessionCommandPipe $pipe
    ) : void
    {
        if (!$pipe->hasCommand($commandName)) {
            $this->say([
                '%name%' => $commandName
            ])->warning('command.notExists');
            return;
        }

        $id = $pipe->getCommandID($commandName);
        $command = $pipe->makeCommand($session, $id);
        $desc = $pipe->getCommandDesc($commandName);
        $this->helpCommand($command->getCommandDefinition(), $desc);
    }

    public function helpCommand(CommandDefinition $definition, string $desc) : void
    {
        $commandName = $definition->getCommandName();
        $output = "命令 [$commandName] : $desc\n\n";


        // 变量
        $arguments = $definition->getArguments();
        if (!empty($arguments)) {
            $output .= "arguments (直接写在变量后, 空格隔开, 字符串建议放在引号内) :\n";
            foreach ($arguments as $argument) {
               $output .= sprintf(
                "  %s\t:\t%s\n",
                    $argument->getName(),
                    $this->say()->trans($argument->getDescription())
                );
            }
        }

        $options = $definition->getOptions();
        if (!empty($options)) {
            $output.="\noptions: (直接写参数名, 例如 -h ) \n";
            foreach ($options as $option) {
                $name = $option->getName();
                $shotCut = $option->getShortcut();
                $shotCutStr = $shotCut
                    ?  "-$shotCut,"
                    : '';


                $output.= sprintf(
                    "  %s--%s\t:\t%s\n",
                    $shotCutStr,
                    $name,
                    $this->say()->trans($option->getDescription())
                );
            }
        }
        $this->say()->info($output);
    }
}