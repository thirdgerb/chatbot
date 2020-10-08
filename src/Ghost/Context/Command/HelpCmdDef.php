<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context\Command;

use Commune\Blueprint\Framework\Command\CommandDef;
use Commune\Blueprint\Framework\Command\CommandMsg;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Framework\Command\AbsHelpCmd;
use Commune\Ghost\Context\ACommandContext;
use Commune\Message\Host\SystemInt\CommandDescInt;
use Commune\Message\Host\SystemInt\CommandListInt;
use Commune\Message\Host\SystemInt\CommandMissInt;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class HelpCmdDef extends AContextCmdDef
{

    public static function stub(): array
    {
        return [
            'desc' => '查看可用指令. 也可以输入 "命令名 -h", 例如"help -h"',
            'signature' => 'help
        {commandName? : 命令的名称.比如 /help }
    ',
        ];
    }


    public function handle(
        Dialog $dialog,
        CommandMsg $message
    ): ? Operator
    {
        $context = $dialog->context;
        $mark = $context->getDef()->getStrategy()->commandMark;
        if (empty($message['commandName'])) {
            return $this->helpContext($dialog, $context, $mark);
        } else {
            return $this->helpCommandName($dialog, $message['commandName'], $context);
        }
    }


    public function helpContext(
        Dialog $dialog,
        Context $context,
        string $commandMark
    ) : Operator
    {
        // @deprecated
        if ($context instanceof ACommandContext) {
            $defs = $context->getContextCmdDefMap();

        } else {
            $map = CommandDefMap::findMap($context->getDef());
            $defs = isset($map)
                ? $map->getContextCmdDefMap()
                : [];
        }

        $messages = [];
        foreach ($defs as $def) {
            $commandDef = $def->getCommandDef();
            $key = $commandMark . $commandDef->getCommandName();
            $messages[$key] = $def->getDescription();
        }


        $cmdList = $this->rangeMessages($messages);
        return $dialog
            ->send()
            ->message(
                CommandListInt::instance($cmdList)
            )
            ->over()
            ->dumb();
    }

    public function helpCommandName(
        Dialog $dialog,
        string $commandName,
        Context $context
    ) : Operator
    {
        $defMap = CommandDefMap::findMap($context->getDef());
        $map = isset($defMap)
            ? $defMap->getContextCmdDefMap()
            : [];

        if (!isset($map[$commandName])) {
            return $dialog
                ->send()
                ->message(CommandMissInt::instance($commandName))
                ->over()
                ->dumb();
        }

        $def = $map[$commandName];
        return $this->helpCommand(
            $dialog,
            $def->getCommandDef(),
            $def->desc
        );
    }


    public function helpCommand(
        Dialog $dialog,
        CommandDef $definition,
        string $desc
    ) : Operator
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

        return $dialog
            ->send()
            ->message(
                CommandDescInt::instance(
                    $commandName,
                    $desc,
                    $this->rangeMessages($args),
                    $this->rangeMessages($opts)
                )
            )
            ->over()
            ->dumb();
    }



    protected function rangeMessages(array $lines) : string
    {
        return AbsHelpCmd::rangeMessages($lines);
    }

}