<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\HostMsg\Convo;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Protocals\HostMsg\ConvoMsg;


/**
 * 用于同步状态的消息
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read array $query
 */
interface ContextMsg extends ConvoMsg
{
    public function getContextId() : string;

    public function getContextName() : string;

    public function getQuery() : array;

    public function getMemorableData() : array;

    public function toContext(Cloner $cloner) : Context;
}