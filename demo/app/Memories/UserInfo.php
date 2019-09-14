<?php

/**
 * Class UserInfo
 * @package Commune\Demo\App\Memories
 */

namespace Commune\Demo\App\Memories;

use Commune\Chatbot\App\Memories\MemoryDef;
use Commune\Chatbot\OOHost\Session\Scope;

/**
 * @property string $name ask.name
 */
class UserInfo extends MemoryDef
{
    const DESCRIPTION = '用户昵称';
    const SCOPE_TYPES = [Scope::USER_ID];
}