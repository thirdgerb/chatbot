<?php


namespace Commune\Chatbot\App\Commands\Navigation;



use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class RepeatCmd extends AbsNavigatorCmd
{
    const SIGNATURE = 'repeat';

    const DESCRIPTION = '重复当前对话';

    protected $sneak = false;

    protected function navigate(Dialog $dialog): Navigator
    {
        return $dialog->repeat();
    }


}