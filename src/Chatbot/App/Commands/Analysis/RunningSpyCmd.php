<?php

namespace Commune\Chatbot\App\Commands\Analysis;


use Commune\Chatbot\Blueprint\Conversation\RunningSpy;
use Commune\Chatbot\Blueprint\Message\Transformed\CommandMsg;
use Commune\Chatbot\Framework\Conversation\RunningSpies;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\Command\SessionCommand;
use Commune\Chatbot\OOHost\Command\SessionCommandPipe;
use Commune\Chatbot\OOHost\Session\Session;

class RunningSpyCmd extends SessionCommand
{
    const SIGNATURE = 'runningSpy
        {--d|detail : 查看所有选项的详情}
    ';

    const DESCRIPTION = '查看一些关键类的实例数量. 用于排查部分内存泄露问题.';

    public function handle(CommandMsg $message, Session $session, SessionCommandPipe $pipe): void
    {
        $detail = $message['--detail'] ?? false;

        $classes = RunningSpies::getSpies();

        $str = '';
        foreach ($classes as $running) {
            if (!is_a($running, RunningSpy::class, TRUE)) {
                throw new ConfigureException("$running is not subclass of ". RunningSpy::class);
            }

            $str .= $this->showRunningTrace(
                $running,
                call_user_func([$running, 'getRunningTraces']),
                $detail
            );
        }

        $this->say()->info($str);
    }

    protected function showRunningTrace(string $type, array $traces, bool $showDetail) : string
    {
        $c = count($traces);

        $slices = array_slice($traces, 0, 20);

        $output = "\n$type 运行中实例共 $c 个";
        if ($showDetail) {
            $output .= "\n列举最多20个如下:\n";
            foreach ($slices as $trace => $id) {
                $output .= "  $trace : $id\n";
            }
        }

        return $output;
    }

}