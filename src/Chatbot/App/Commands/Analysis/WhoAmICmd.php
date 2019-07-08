<?php


namespace Commune\Chatbot\App\Commands\Analysis;



use Commune\Chatbot\Blueprint\Message\Command\CmdMessage;
use Commune\Chatbot\OOHost\Command\SessionCommand;
use Commune\Chatbot\OOHost\Command\SessionCommandPipe;
use Commune\Chatbot\OOHost\Session\Session;

class WhoAmICmd extends SessionCommand
{
    const SIGNATURE = 'whoami';

    const DESCRIPTION = '查看用户自己的数据';

    public function handle(CmdMessage $message, Session $session, SessionCommandPipe $pipe): void
    {
        $user = $session->conversation->getUser();

        $this->say()->info(
            "您的数据: \n"
            . $user->toPrettyJson()
        );
    }


}