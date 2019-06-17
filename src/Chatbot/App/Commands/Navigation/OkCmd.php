<?php


namespace Commune\Chatbot\App\Commands\Navigation;


use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class OkCmd extends AbsNavigatorCmd
{
    const SIGNATURE = 'ok';

    const DESCRIPTION = '结束当前context';

    protected function navigate(Dialog $dialog): Navigator
    {
        return $dialog->fulfill();
    }


}