<?php


namespace Commune\Chatbot\App\Commands\Navigation;



use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class BackCmd extends AbsNavigatorCmd
{
    const SIGNATURE = 'back';

    const DESCRIPTION = '回到上一个问题.';

    protected function navigate(Dialog $dialog): Navigator
    {
        return $dialog->backward();
    }


}