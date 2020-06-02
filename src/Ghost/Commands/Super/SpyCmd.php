<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Commands\Super;

use Commune\Ghost\Cmd\AGhostCmd;
use Commune\Support\RunningSpy\Spied;
use Commune\Support\RunningSpy\SpyAgency;
use Commune\Blueprint\Exceptions\CommuneLogicException;
use Commune\Blueprint\Framework\Command\CommandMsg;
use Commune\Blueprint\Framework\Pipes\RequestCmdPipe;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SpyCmd extends AGhostCmd
{
    const SIGNATURE = 'spy
        {--d|detail : 查看所有选项的详情}
    ';

    const DESCRIPTION = '查看一些关键类的实例数量. 用于排查部分内存泄露问题.';

    protected function handle(CommandMsg $command, RequestCmdPipe $pipe): void
    {
        $isRunning = SpyAgency::isRunning();

        if (! $isRunning) {
            $this->error('runningSpy is not running');
            return;
        }

        $detail = $message['--detail'] ?? false;

        $classes = SpyAgency::getSpies();

        $str = '';
        foreach ($classes as $running) {
            if (!is_a($running, Spied::class, TRUE)) {
                throw new CommuneLogicException(__METHOD__ . " : $running is not subclass of " . Spied::class);
            }

            $str .= $this->showRunningTrace(
                $running,
                call_user_func([$running, 'getRunningTraces']),
                $detail
            );
        }

        $this->info($str);
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