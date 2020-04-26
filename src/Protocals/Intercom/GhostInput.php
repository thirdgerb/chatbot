<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\Intercom;

use Commune\Protocals\Comprehension;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * # 默认属性
 * @see GhostMsg
 *
 * # 请求的 shell 相关信息
 * @property-read string $sceneId       请求所处的场景ID
 * @property-read array $env            从 Shell 传入的环境变量.
 *
 * # 抽象
 * @property-read Comprehension $comprehension  传递过来的语境理解.
 */
interface GhostInput extends GhostMsg
{

}