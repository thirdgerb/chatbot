<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Context;

use ArrayAccess;
use Commune\Ghost\Blueprint\Memory\Recollection;
use Commune\Ghost\Blueprint\Definition\ContextDef;
use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Ghost\Blueprint\Session\SessionInstance;
use Commune\Support\DI\Injectable;

/**
 * 当前语境. 用来读写当前语境的变量.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Context extends ArrayAccess, ArrayAndJsonAble, SessionInstance, Injectable
{
    public function isPrepared() : bool;

    public function getId() : string;

    public function getDef() : ContextDef;

    public function getRecollection() : Recollection;
}