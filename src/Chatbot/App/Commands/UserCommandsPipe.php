<?php


namespace Commune\Chatbot\App\Commands;


use Commune\Chatbot\App\Components\Predefined\Navigation\BackwardInt;
use Commune\Chatbot\App\Components\Predefined\Navigation\CancelInt;
use Commune\Chatbot\App\Components\Predefined\Navigation\QuitInt;
use Commune\Chatbot\App\Components\Predefined\Navigation\RepeatInt;
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
    ];

    // 定义一个 command mark
    protected $commandMark = '#';



}