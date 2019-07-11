<?php


namespace Commune\Chatbot\App\Commands\Analysis;


use Commune\Chatbot\Blueprint\Message\Command\CmdMessage;
use Commune\Chatbot\OOHost\Command\SessionCommand;
use Commune\Chatbot\OOHost\Command\SessionCommandPipe;
use Commune\Chatbot\OOHost\Session\Session;

class WhereCmd extends SessionCommand
{
    const SIGNATURE = 'where 
            {--s|scope : 查看当前 scope}
            {--n|snapshot : 查看当前 snapshot}
            {--c|context : 查看当前 context}
            {--a|cached : 查看 cache 到snapshot的数据}
            {--b|breakpoint : 查看当前 breakpoint}';

    const DESCRIPTION = '查看维持多轮对话的关键数据.';

    public function handle(CmdMessage $message, Session $session, SessionCommandPipe $pipe): void
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
                        $session->repo->snapshot,
                        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
                    )
                );
        }


        if ($message['--context']) {
            $talk
                ->info('context data is :')
                ->info($session->dialog->currentContext()->toPrettyJson());
        }

        if ($message['--breakpoint']) {
            $talk
                ->info('breakpoint data is :')
                ->info($session->repo->snapshot->breakpoint->toPrettyJson());
        }

        if ($message['--cached']) {
            $s = json_encode(
                $session->repo->snapshot->cachedSessionData,
                JSON_PRETTY_PRINT
                    | JSON_UNESCAPED_SLASHES
                    |JSON_UNESCAPED_UNICODE
            );
            $talk
                ->info("snapshot cached data is :\n" . $s);

        }

        if ($message->isEmpty()) {
            $talk->info("需要结合参数, 请输入 where -h 查看参数");
        }

        $session->beSneak();
    }

}