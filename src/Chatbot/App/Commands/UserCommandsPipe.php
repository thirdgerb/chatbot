<?php


namespace Commune\Chatbot\App\Commands;


use Commune\Chatbot\App\Commands\Navigation\BackCmd;
use Commune\Chatbot\App\Commands\Navigation\CancelCmd;
use Commune\Chatbot\App\Commands\Navigation\QuitCmd;
use Commune\Chatbot\OOHost\Command\Help;
use Commune\Chatbot\OOHost\Command\SessionCommandPipe;

class UserCommandsPipe extends SessionCommandPipe
{
    // 命令的名称.
    protected $commands = [
        Help::class,
        BackCmd::class,
        QuitCmd::class,
        CancelCmd::class,
    ];

    // 定义一个 command mark
    protected $commandMark = '#';



}