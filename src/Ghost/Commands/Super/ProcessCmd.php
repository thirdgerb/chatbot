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

use Commune\Blueprint\Framework\Command\CommandMsg;
use Commune\Blueprint\Framework\Pipes\RequestCmdPipe;
use Commune\Ghost\Cmd\AGhostCmd;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ProcessCmd extends AGhostCmd
{
    const SIGNATURE = 'process 
       {--l|len : 检查长度}
       {--s|serialize : 检查序列化压缩结果}
    ';

    const DESCRIPTION = '查看多轮对话进程的数据';


    protected function handle(CommandMsg $message, RequestCmdPipe $pipe): void
    {
        $process = $this->cloner->runtime->getCurrentProcess();
        $this->info($process->toPrettyJson());

        $json = $process->toJson();

        if (!empty($message['--len'])) {
            $this->info(
                "process to json mb_string length : {len}",
                ['len' => mb_strlen($json)]
            );
        }

        $gz = $message['--gz'] ?? false;

        if (!empty($message['--serialize']) || $gz) {

            // 序列化
            $a = microtime(true);
            $serialized = serialize($process);
            $gzStr = gzcompress($serialized);
            $b = microtime(true);
            $serializedTime = round(($b - $a) * 1000000);

            $this->info(
                "process to serialized string length : {len}, time: {time}ws ",
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
                "process unserialize time is {time}ws",
                ['time' => $time]
            );
        }


    }
}