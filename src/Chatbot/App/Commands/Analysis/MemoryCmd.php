<?php


namespace Commune\Chatbot\App\Commands\Analysis;


use Commune\Chatbot\Blueprint\Message\Command\CmdMessage;
use Commune\Chatbot\OOHost\Command\SessionCommand;
use Commune\Chatbot\OOHost\Command\SessionCommandPipe;
use Commune\Chatbot\OOHost\Context\Memory\MemoryRegistrar;
use Commune\Chatbot\OOHost\Session\Session;

class MemoryCmd extends SessionCommand
{
    const SIGNATURE = 'memory
        {name? : 查看某个memory 的信息}
        {--a|available : 查看所有可用的memory}';


    const DESCRIPTION = '查看session当前的memory';



    public function handle(CmdMessage $message, Session $session, SessionCommandPipe $pipe): void
    {
        $name = $message['name'];

        if ($name) {
            $memory = $session->memory[$name];

            if (isset($memory)) {
                $this->say()
                    ->info("memory $name data is ")
                    ->info($memory->toPrettyJson());
            } else {
                $this->say()
                    ->info("memory $name is not predefined");
            }

            return;
        }

        if ($message['--available']) {
            $talk = $this->say();
            $talk->info("defined memory names are:");
            foreach(MemoryRegistrar::getIns()->each() as $def) {
                $talk->info($def->getName());
            }
            return;
        }

        $this->say()->info("请输入 -h 查看命令");
    }


}