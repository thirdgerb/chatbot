<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Cloner;

use Commune\Blueprint\Ghost\Ucl;


/**
 * 当前请求的场景信息.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Ucl $entry        入口路径, 同时也作为根路径
 * @property-read array $env        环境变量
 */
interface ClonerScene
{
}