<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\GhostCmd\Super;

use Commune\Blueprint\Framework\Command\CommandMsg;
use Commune\Blueprint\Framework\Pipes\RequestCmdPipe;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Runtime\Waiter;
use Commune\Kernel\GhostCmd\AGhostCmd;
use Commune\Message\Host\Convo\Verbal\JsonMsg;
use Commune\Support\Arr\ArrayAndJsonAble;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ProcessCmd extends AGhostCmd
{
    const SIGNATURE = 'process 
       {--w|waiter : 当前语境的状态}
       {--b|backtrace : 检查返回路径}
       {--s|serialize : 检查序列化压缩结果}
    ';

    const DESCRIPTION = '查看多轮对话进程的数据';


    protected function handle(CommandMsg $message, RequestCmdPipe $pipe): void
    {
        $process = $this->cloner->runtime->getCurrentProcess();

        $w = $message['--waiter'] ?? false;
        $b = $message['--backtrace'] ?? false;
        $s = $message['--serialize'] ?? false;

        if ($w) {
            $this->showWaiter($process);
            return;
        } elseif($b) {
            $this->showBacktrace($process);
        } elseif($s) {
            $this->showSerialize($process);
        } else {
            $this->output(JsonMsg::instance($process->toPrettyJson()));
            $json = $process->toJson();
            $this->info(
                "process to json mb_string length : {len}",
                ['len' => mb_strlen($json)]
            );
        }
    }

    protected function showWaiter(Process $process) : void
    {
        $waiter = $process->waiter;
        if (empty($waiter)) {
            $this->info("process has no waiter");
        } else {
            $this->output(JsonMsg::instance($waiter->toPrettyJson()));
        }
    }

    protected function showBacktrace(Process $process) : void
    {
        $backtrace = $process->backtrace;
        $this->output(JsonMsg::instance(json_encode($backtrace, ArrayAndJsonAble::PRETTY_JSON)));
    }

    protected function showSerialize(Process $process) : void
    {

        // 序列化
        $a = microtime(true);
        $serialized = serialize($process);
        $gzStr = gzcompress($serialized);
        $b = microtime(true);
        $serializedTime = round(($b - $a) * 1000000);

        $this->info(
            "process to serialized string length : {len}, time: {time}us ",
            ['len' => strlen($serialized), 'time' => $serializedTime]
        );

        // 输出序列化字符串
        $this->info("serialized: $serialized");

        $len = strlen($gzStr);

        // gz compress 检查.
        $this->info(
            "process to gzcompress string length : {len}",
            ['len' => $len]
        );


        // 反序列化检查.
        $a = microtime(true);
        $un = gzuncompress($gzStr);
        $unProcess = unserialize($un);
        $b = microtime(true);
        $time = round(($b - $a) * 1000000);
        $this->info(
            "process gzuncompress equals serialized : {bool}",
            ['bool' => $serialized === $un ? "true" : "false"]
        );

        $this->info(
            "process unserialize time is {time}us",
            ['time' => $time]
        );
    }
}