<?php


namespace Commune\Chatbot\App\Commands;


use Commune\Chatbot\App\Commands\Analysis\WhoAmICmd;
use Commune\Components\Predefined\Intents\Navigation\BackwardInt;
use Commune\Components\Predefined\Intents\Navigation\CancelInt;
use Commune\Components\Predefined\Intents\Navigation\QuitInt;
use Commune\Components\Predefined\Intents\Navigation\RepeatInt;
use Commune\Components\Predefined\Intents\Navigation\RestartInt;
use Commune\Chatbot\OOHost\Command\HelpCmd;
use Commune\Chatbot\OOHost\Command\SessionCommandPipe;

class UserCommandsPipe extends SessionCommandPipe
{
    // 命令的名称.
    protected $commands = [
        HelpCmd::class,
        CancelInt::class,
        QuitInt::class,
        BackwardInt::class,
        RepeatInt::class,
        RestartInt::class,
        WhoAmICmd::class,
    ];

    // 定义一个 command mark
    protected $commandMark = '#';



}