<?php


namespace Commune\Chatbot\App\Commands\Analysis;


use Commune\Chatbot\Blueprint\Message\Transformed\CommandMsg;
use Commune\Chatbot\OOHost\Command\SessionCommand;
use Commune\Chatbot\OOHost\Command\SessionCommandPipe;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Chatbot\OOHost\Session\Snapshot;
use Commune\Support\Arr\ArrayAndJsonAble;

class WhereCmd extends SessionCommand
{
    const SIGNATURE = 'where 
            {--s|scope : 查看当前 scope}
            {--n|snapshot : 查看当前 snapshot}
            {--c|context : 查看当前 context}
            {--p|process : 查看当前 breakpoint::process}
            ';

    const DESCRIPTION = '查看维持多轮对话的关键数据.';

    public function handle(CommandMsg $message, Session $session, SessionCommandPipe $pipe): void
    {

        $talk = $session->dialog->say();
        if ($message['--scope']) {
            $talk->info('scope is :')
                ->info($session->scope->toPrettyJson());
        }

        if ($message['--snapshot']) {
            $talk->info('snapshot is :')
                ->info(
                    json_encode(
                        array_map(function(Snapshot $snapshot){
                            return serialize($snapshot);
                        }, $session->repo->getSnapshots()),
                        ArrayAndJsonAble::PRETTY_JSON
                    )
                );
        }

        if ($message['--process']) {
            $snapshots = $session->repo->getSnapshots();
            /**
             * @var Snapshot $snapshot
             */
            $snapshot = current($snapshots);
            $talk->info('process is : ')
                ->info($snapshot->breakpoint->process()->toPrettyJson());
        }

        if ($message['--context']) {
            $talk
                ->info('context data is :')
                ->info($session->dialog->currentContext()->toPrettyJson());
        }


        if ($message->isEmpty()) {
            $talk->info("需要结合参数, 请输入 where -h 查看参数");
        }

        $session->beSneak();
    }

}