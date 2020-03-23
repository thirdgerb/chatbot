<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Internal;

use Commune\Message\Abstracted\Comprehension;
use Commune\Message\Convo\ConvoMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Scope $scope                      消息的作用域
 * @property-read ConvoMsg $message                 Shell平台来的通讯消息
 * @property-read Comprehension $comprehension      对消息的高级抽象
 * @property-read array $env                        环境变量
 */
interface IncomingMsg
{

}