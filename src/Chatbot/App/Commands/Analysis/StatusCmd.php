<?php


namespace Commune\Chatbot\App\Commands\Analysis;


use Commune\Chatbot\Blueprint\Message\Command\CmdMessage;
use Commune\Chatbot\OOHost\Command\SessionCommand;
use Commune\Chatbot\OOHost\Command\SessionCommandPipe;
use Commune\Chatbot\OOHost\Context\AbsContext;
use Commune\Chatbot\OOHost\Session\Session;

class StatusCmd extends SessionCommand
{
    const SIGNATURE = 'status
        {--c|conversation : 检查conversation 的健康情况.}
        {--t|context : 检查context 的实例数量.}
        {--s|session : 检查session 的健康情况.}';


    const DESCRIPTION = '查看 chat app 当前状态';



    public function handle(CmdMessage $message, Session $session, SessionCommandPipe $pipe): void
    {
        if ($message['--conversation']) {
            $this->showConversation($session);
        }

        if ($message['--session']) {
            $this->showSession($session);
        }

        if ($message['--context']) {
            $this->showContext($session);

        }
    }

    protected function showConversation(Session $session) : void
    {
        $talk = $session->dialog->say();
        $ids = $session->conversation->getInstanceIds();
        $c = count($ids);
        $talk->info("conversation ids : $c");
        $i = 0;
        foreach($ids as $trace => $id) {
            $talk->info("trace $trace: $id");
            $i ++;
            if ($i > 20) {
                return;
            }
        }
    }

    protected function showSession(Session $session) : void
    {
        $talk = $session->dialog->say();
        $ids = $session->getInstanceIds();
        $c = count($ids);
        $talk->info("session ids : $c ");
        $i = 0;
        foreach ($ids as $trace => $id) {
            $talk->info("trace $trace: $id");
            $i ++;
            if ($i > 20) {
                return;
            }
        }

    }

    protected function showContext(Session $session) : void
    {
        $talk = $session->dialog->say();
        $talk->info("context counts : ");
        foreach (AbsContext::getInstancesCount() as $name => $count) {
            $talk->info("type $name: $count");
        }
    }
}