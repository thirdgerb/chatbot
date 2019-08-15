<?php

namespace Commune\Chatbot\App\Commands\Analysis;


use Commune\Chatbot\Blueprint\Conversation\RunningSpy;
use Commune\Chatbot\Blueprint\Message\Command\CmdMessage;
use Commune\Chatbot\Framework\Conversation\ConversationImpl;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\Command\SessionCommand;
use Commune\Chatbot\OOHost\Command\SessionCommandPipe;
use Commune\Chatbot\OOHost\Dialogue\DialogImpl;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\SessionImpl;

class RunningSpyCmd extends SessionCommand
{
    const SIGNATURE = 'runningSpy
        {--d|detail : 查看所有选项的详情}
    ';

    const DESCRIPTION = '查看一些关键类的实例数量. 用于排查部分内存泄露问题.';

    protected $classes = [
        ConversationImpl::class,
        SessionImpl::class,
        DialogImpl::class,
    ];

    public function handle(CmdMessage $message, Session $session, SessionCommandPipe $pipe): void
    {
        $detail = $message['--detail'] ?? false;

        foreach ($this->classes as $running) {
            if (!is_a($running, RunningSpy::class, TRUE)) {
                throw new ConfigureException("$running is not subclass of ". RunningSpy::class);
            }

            $this->showRunningTrace(
                $running,
                call_user_func([$running, 'getRunningTraces']),
                $detail
            );
        }
    }

    protected function showRunningTrace(string $type, array $traces, bool $showDetail) : void
    {
        $c = count($traces);

        $slices = array_slice($traces, 0, 20);

        $output = "$type 运行中实例共 $c 个 \n";
        if ($showDetail) {
            $output .= "列举最多20个如下:\n";
            foreach ($slices as $trace => $id) {
                $output .= "  $trace : $id\n";
            }
        }

        $this->say()
            ->info($output);
    }

}