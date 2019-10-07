<?php


namespace Commune\Chatbot\OOHost\Command;


use Commune\Chatbot\Blueprint\Message\Command\CmdMessage;
use Commune\Chatbot\OOHost\Session\Session;

/**
 * 帮助命令. 查看当前可用的命令. 或者查看当前命令的选项.
 */
class HelpCmd extends SessionCommand
{
    const SIGNATURE = 'help
        {commandName? : 命令的名称.比如 /help }
    ';

    const DESCRIPTION = '查看可用指令. 也可以输入 "命令名 -h", 例如"help -h"';

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
        SessionCommandPipe $pipe
    ) : void
    {
        if (!$pipe->hasCommand($commandName)) {
            $this->say([
                'name' => $commandName
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
        $output = "命令 [$commandName] : $desc\n";
        $speech = $this->say();


        // 变量
        $arguments = $definition->getArguments();
        if (!empty($arguments)) {
            $output .= "\narguments (直接写在变量后, 空格隔开, 字符串建议放在引号内) :\n";


            $messages = [];
            foreach ($arguments as $argument) {
                $messages[$argument->getName()] = $speech->trans($argument->getDescription());
            }

            $output .= $this->rangeMessages($messages);
        }

        $options = $definition->getOptions();
        if (!empty($options)) {
            $output.="\noptions: (直接写参数名, 例如 -h ) \n";

            $messages = [];
            foreach ($options as $option) {
                $name = $option->getName();
                $shotCut = $option->getShortcut();
                $shotCutStr = $shotCut
                    ?  "-$shotCut,"
                    : '';

                $key = "$shotCutStr--$name";
                $messages[$key] = $speech->trans($option->getDescription());
            }

            $output.= $this->rangeMessages($messages);
        }
        $this->say()->info($output);
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