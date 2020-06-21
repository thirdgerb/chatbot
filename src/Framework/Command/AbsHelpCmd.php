<?php


namespace Commune\Framework\Command;


use Commune\Blueprint\Framework\Command\CommandDef;
use Commune\Blueprint\Framework\Command\CommandMsg;
use Commune\Blueprint\Framework\Pipes\RequestCmdPipe;
use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Message\Host\SystemInt\CommandDescInt;
use Commune\Message\Host\SystemInt\CommandListInt;
use Commune\Message\Host\SystemInt\CommandMissInt;

/**
 * 帮助命令. 查看当前可用的命令. 或者查看当前命令的选项.
 */
abstract class AbsHelpCmd extends ARequestCmd
{
    const SIGNATURE = 'help
        {commandName? : 命令的名称.比如 /help }
    ';

    const DESCRIPTION = '查看可用指令. 也可以输入 "命令名 -h", 例如"help -h"';

    public function descCommand(
        AppRequest $request,
        CommandDef $def,
        string $desc
    ) : ? AppResponse
    {

        $this->helpCommand($def, $desc);
        return $this->response($request, $this->outputs);
    }

    protected function getHelpCmd(): AbsHelpCmd
    {
        return $this;
    }


    public function handle(CommandMsg $message, RequestCmdPipe $pipe) : void
    {
        if (empty($message['commandName'])) {
            $this->helpPipe($pipe);
        } else {
            $this->helpCommandName($message['commandName'], $pipe);
        }
    }

    public function helpPipe(RequestCmdPipe $pipe) : void
    {
        $mark = $pipe->getCommandMark();
        $messages = [];
        foreach ($pipe->getDescriptions() as $name => $description) {
            $messages[$mark . $name] = $description;
        }

        $cmdList = $this->rangeMessages($messages);
        $this->output(CommandListInt::instance($cmdList));
    }

    public function helpCommandName(
        string $commandName,
        RequestCmdPipe $pipe
    ) : void
    {
        if (!$pipe->hasCommand($commandName)) {
            $this->output(CommandMissInt::instance($commandName));
        }

        $id = $pipe->getCommandID($commandName);
        $command = $this->getContainer()->make($id);
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
             CommandDescInt::instance(
                $commandName,
                $desc,
                $this->rangeMessages($args),
                $this->rangeMessages($opts)
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