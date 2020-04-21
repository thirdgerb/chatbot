<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\Message\Convo;

use Commune\Protocals\Message\ConvoProto;


/**
 * 用于同步状态的消息
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $contextName       语境名称
 * @property-read string $contextId         语境Id
 * @property-read array $data               语境的数据.
 */
interface ContextProto extends ConvoProto
{
}