<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Runtime;


/**
 * 能够反映单轮对话终态
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read
 * @property-read array $blocking       响应回调 onRetain
 * @property-read array $watching       前序响应 onWatch
 * @property-read array $current         等待中的.
 * @property-read array $sleeping       可以被 wake 的  onWake
 * @property-read array $gc             可以终止 GC 的  onRestore
 * @property-read array $depending      等待回调的.
 *
 */
interface EventMap
{

}