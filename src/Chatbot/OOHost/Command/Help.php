<?php


namespace Commune\Chatbot\OOHost\Command;


use Commune\Chatbot\Blueprint\Message\Command\CmdMessage;
use Commune\Chatbot\OOHost\Session\Session;

class Help extends SessionCommand
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
            $this->helpCommandName($message['commandName'], $pipe);
        }
    }

    public function helpPipe( SessionCommandPipe $pipe) : void
    {
        $available = '';
        foreach ($pipe->getDescriptions() as $name => $description) {
            $available .= "$name\t: \t$description" .PHP_EOL;
        }

        $this->say(['%available%' => $available])
            ->info('command.available');
    }

    public function helpCommandName(
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

        $clazz = $pipe->getCommandClazz($commandName);
        $desc = $pipe->getCommandDesc($commandName);

        if (!is_a($clazz, SessionCommand::class, TRUE)) {
            $this->say()->warning('command.notValid', [
                '%name%' => $commandName
            ]);
            return;
        }

        $this->helpCommandClazz($clazz, $desc);
    }

    public function helpCommandClazz(string $clazz, string $desc) : void
    {
        $getDefinition = "$clazz::getCommandDefinition";
        /**
         * @var CommandDefinition $definition
         */
        $definition = $getDefinition();

        $commandName = $definition->getCommandName();
        $this->say()->info("Command [$commandName] : $desc" . PHP_EOL);


        // 变量
        $this->say()->info("Arguments:");
        foreach ($definition->getArguments() as $argument) {
            $this->say()
                ->info(
                    sprintf(
                    "%s\t: %s",
                        $argument->getName(),
                        $this->say()->trans($argument->getDescription())
                    )
                );
        }
        $this->say()->info("");

        $this->say()->info("Options:");
        foreach ($definition->getOptions() as $option) {
            $name = $option->getName();
            $shotCut = $option->getShortcut();
            $shotCutStr = $shotCut
                ?  "-$shotCut,"
                : '';


            $this->say()->info(
                sprintf(
                    "%s--%s \t: %s",
                    $shotCutStr,
                    $name,
                    $this->say()->trans($option->getDescription())
                )
            );
        }


    }
}