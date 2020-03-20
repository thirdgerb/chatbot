<?php


namespace Commune\Chatbot\App\Commands\Navigation;


use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class CancelCmd extends AbsNavigatorCmd
{
    const SIGNATURE = 'cancel';

    const DESCRIPTION = '取消当前context';

    protected function navigate(Dialog $dialog): Navigator
    {
        return $dialog->cancel();
    }


}