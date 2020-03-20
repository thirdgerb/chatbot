<?php


namespace Commune\Chatbot\App\Commands\Navigation;



use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class QuitCmd extends AbsNavigatorCmd
{
    const SIGNATURE = 'quit';

    const DESCRIPTION = '退出会话';

    protected function navigate(Dialog $dialog): Navigator
    {
        return $dialog->quit();
    }


}