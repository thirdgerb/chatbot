<?php


namespace Commune\Demo\App\Components\CustomService\Memories;


use Commune\Chatbot\App\Memories\MemoryDef;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Session\Scope;

/**
 * @property-read string $name
 * @property-read bool $vip
 */
class UserInfo extends MemoryDef
{
    const SCOPE_TYPES = [Scope::USER_ID];


    public function __askName(Dialog $dialog) : void
    {
        $dialog->say()->askVerbose('请告诉我您测试用的名字');
    }

    public function __askVip(Dialog $dialog) : void
    {
        $dialog->say()->askConfirm('请确定您测试的身份是否为VIP');
    }
}