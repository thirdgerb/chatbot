<?php

namespace Commune\Components\Predefined\Memories;


use Commune\Chatbot\App\Memories\MemoryDef;
use Commune\Chatbot\OOHost\Session\Scope;

/**
 * @property string $name ask.name
 * @property int $loginTimes
 */
class UserInfoMem extends MemoryDef
{
    const DESCRIPTION = '用户基本信息';

    const SCOPE_TYPES = [Scope::USER_ID];

    protected function init(): array
    {
        return [
            'loginTimes' => 0,
        ];
    }

    public function increaseLoginTimes() : void
    {
        $this->loginTimes = $this->loginTimes + 1;
    }

}