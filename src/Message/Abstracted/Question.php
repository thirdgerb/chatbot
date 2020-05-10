<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\Abstracted;

use Commune\Protocals\Abstracted;


/**
 * 将用户的消息理解成为一个问题
 *
 * 问题和意图有所区别, 主要有以下原因:
 *
 * 1. 问题的确可以做成一种意图.
 * 1. 问题的发现可能更容易基于规则, 比如用 "?", "吗" 之类的特殊字符结尾.
 * 1. 问题的匹配算法和意图可能不一样, 因此独立出来. 比如基于词频的搜索可能更有效.
 * 1. 问题的响应逻辑和意图往往不同. 问题很可能是上下文无关的.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string|null $query            问题描述
 * @property-read string|null $queryId          问题的 ID
 * @property-read array       $entities         问题的实体
 * @property-read string[]    $suggestions      用户给的答案建议. 通常会有吗?
 */
interface Question extends Abstracted
{

}