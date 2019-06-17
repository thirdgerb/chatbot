<?php


namespace Commune\Chatbot\App\Commands;


use Commune\Chatbot\App\Abilities\Supervise;
use Commune\Chatbot\App\Commands\Analysis\MemoryCmd;
use Commune\Chatbot\App\Commands\Analysis\RedirectCmd;
use Commune\Chatbot\App\Commands\Analysis\StatusCmd;
use Commune\Chatbot\App\Commands\Analysis\WhereCmd;
use Commune\Chatbot\OOHost\Command\Help;
use Commune\Chatbot\OOHost\Command\SessionCommandPipe;
use Commune\Chatbot\OOHost\Session\Session;

class AnalyserPipe extends SessionCommandPipe
{
    // 命令的名称.
    protected $commands = [
        Help::class,
        WhereCmd::class,
        StatusCmd::class,
        MemoryCmd::class,
        RedirectCmd::class
    ];

    // 定义一个 command mark
    protected $commandMark = '/';

    public function handle(Session $session, \Closure $next): Session
    {
        $isSupervisor = $session->conversation
            ->isAbleTo(Supervise::class);

        if (!$isSupervisor) {
            return $next($session);
        }

        return parent::handle($session, $next);
    }


}