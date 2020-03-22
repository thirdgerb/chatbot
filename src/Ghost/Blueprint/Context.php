<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint;

use Commune\Ghost\Blueprint\Definitions\ContextDef;
use Commune\Ghost\Blueprint\Session\SessionInstance;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * Context (上下文语境) 的容器.
 *
 * 承担唯一的功能: 数据容器.
 * 也可以作为调度工具来使用.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Context extends ArrayAndJsonAble, SessionInstance
{

    public function getId() : string;

    public function getDef() : ContextDef;


}