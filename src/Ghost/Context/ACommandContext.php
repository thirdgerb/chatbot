<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\MindMeta\ContextMeta;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\Context\Codable\ICodeContextDef;
use Commune\Ghost\Context\Command\AContextCmdDef;
use Commune\Ghost\Context\Command\HelpCmdDef;
use Commune\Ghost\Support\CommandUtils;
use Commune\Message\Host\SystemInt\CommandErrorInt;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;

/**
 * 可以使用命令行的 Context.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ACommandContext extends ACodeContext
{

    /**
     * 在命令前的参数.
     * @var string
     */
    protected static $_command_mark = '';



    /**
     * @var AContextCmdDef[][]
     */
    private static $_contextCmdMaps = [];


    /**
     * @return AContextCmdDef[]
     */
    abstract public static function __command_defs() : array;

    /**
     * @return AContextCmdDef[]
     */
    final public static function getContextCmdDefMap() : array
    {
        $selfName = static::class;
        if (array_key_exists($selfName, static::$_contextCmdMaps)) {
            return self::$_contextCmdMaps[$selfName];
        }

        $helpDef = new HelpCmdDef();
        $helpName = $helpDef->getCommandDef()->getCommandName();
        self::$_contextCmdMaps[$selfName][$helpName] = $helpDef;

        $defs = static::__command_defs();

        foreach ($defs as $def) {
            $name = $def->getCommandDef()->getCommandName();
            self::$_contextCmdMaps[$selfName][$name] = $def;
        }

        return self::$_contextCmdMaps[$selfName];
    }

    public static function __make_def(ContextMeta $meta = null): ICodeContextDef
    {
        $def = parent::__make_def($meta);
        $fallbackArr = $def->strategy->heedFallbackStrategies ?? [];
        $fallbackArr[] = static::class . '::commandFallback';
        $def->strategy->heedFallbackStrategies = array_unique($fallbackArr);
        return $def;
    }


    public static function commandFallback(Dialog $dialog) : ? Operator
    {
        $input = $dialog->cloner->input;
        $message = $input->getMessage();

        if (!$message instanceof VerbalMsg) {
            return null;
        }

        $text = $message->getText();
        $text = trim($text);

        if (empty($text)) {
            return null;
        }

        $cmdStr = CommandUtils::getCommandStr($text, static::$_command_mark);
        $cmdName = CommandUtils::getCommandNameStr($cmdStr);

        if (empty($cmdName)) {
            return null;
        }

        $map = static::getContextCmdDefMap();

        if (!isset($map[$cmdName])) {
            return null;
        }

        $def = $map[$cmdName];
        $commandMessage = $def->getCommandDef()->parseCommandMessage($cmdStr);

        if ($commandMessage->isCorrect()) {
            return $def->handle($dialog, $commandMessage);
        }


        $errorBag = $commandMessage->getErrors();

        $error = '';
        foreach ($errorBag as $type => $errors) {
            $error .= PHP_EOL . $type;
            $msg = is_array($errors) ? implode(PHP_EOL.'  ', $errors) : $errors;
            $error .= PHP_EOL . $msg;
        }

        return $dialog
            ->send()
            ->message(
                CommandErrorInt::instance(
                    $cmdName,
                    $error
                )
            )
            ->over()
            ->dumb();
    }



}