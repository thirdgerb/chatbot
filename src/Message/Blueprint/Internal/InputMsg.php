<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Blueprint\Internal;

use Commune\Message\Blueprint\Abstracted\Comprehension;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Comprehension $comprehended        对消息的高级抽象
 * @property-read string $sceneId
 * @property-read array $sceneEnv                        环境变量
 *
 * @mixin InternalMsg
 */
interface InputMsg extends InternalMsg
{
}