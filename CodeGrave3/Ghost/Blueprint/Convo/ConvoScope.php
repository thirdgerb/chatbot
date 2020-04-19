<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Convo;


/**
 * 当前会话的作用域. 通常决定某个上下文记忆的位置.
 * 只包含长程维度, 不包含 messageId, sessionId 等维度.
 * 因为后者都会存储在缓存中间.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * # 维度相关信息.
 *
 * @property-read string $chatbotName   机器人的名字
 * @property-read string $cloneId       机器人分身的 ID
 * @property-read string $shellName     和输入平台有关的信息.
 * @property-read string $userId        用户的唯一 ID
 *
 * # 日期相关
 *
 * @property-read string $date
 * @property-read string $year
 * @property-read string $month
 * @property-read string $day
 * @property-read string $week
 * @property-read string $weekDay
 * @property-read string $hour
 * @property-read string $minute
 */
interface ConvoScope
{
}