<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Session;


/**
 * Session 当前的作用域.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $chatbotName   机器人的名称
 * @property-read string $chatId        聊天的ID, Ghost的 chatId
 * @property-read string $shellName     当前对话所处的 shell
 * @property-read string $userId        用户的 Id
 * @property-read string $sessionId     当前轮次所属会话的 ID
 * @property-read string $sceneId       当前对话轮次的场景 ID
 * @property-read string $messageId     当前对话轮次的消息 ID
 *
 * # 时间相关记忆
 *
 * @property-read string $year          年
 * @property-read string $month         月
 * @property-read string $day           日
 * @property-read string $week          周
 * @property-read string $weekDay       周几
 * @property-read string $hour          小时
 * @property-read string $minute        分钟
 */
interface GhtSessionScope
{
    // scopes
    const CHATBOT_NAME = 'chatbotName';
    const CHAT_ID = 'chatId';
    const SHELL_NAME = 'shellName';
    const USER_ID = 'userId';
    const SESSION_ID = 'sessionId';
    const SCENE_ID = 'sceneId';
    const MESSAGE_ID = 'messageId';

    const YEAR = 'year';
    const MONTH = 'month';
    const DAY = 'day';
    const WEEK = 'week';
    const WEEK_DAY = 'weekDay';
    const HOUR = 'hour';
    const MINUTE = 'minute';



    public function makeScopeId(array $scopes) : string;

}