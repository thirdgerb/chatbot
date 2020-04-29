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

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 当前 Ghost 分身的作用域.
 *
 * @property-read string $cloneId       Clone 的 Id.
 * @property-read string $sessionId
 *
 * @property-read string $senderId      消息发送者的 Id, 平台相关
 * @property-read string $guestId       消息发送者在 Clone 里的 GuestId, 平台无关.
 *
 * @property-read string $shellName     输入消息产生的 Shell 名
 * @property-read string $shellId       输入消息对应的 Shell Id
 * @property-read string $sceneId
 *
 *
 * @property-read string $year
 * @property-read string $month
 * @property-read string $monthDay
 * @property-read string $weekday
 * @property-read string $week
 * @property-read string $hour
 * @property-read string $minute
 *
 */
interface ClonerScope
{
    // 长程维度.
    const CLONE_ID = 'cloneId';
    const SENDER_ID = 'senderId';
    const GUEST_ID = 'guestId';
    const SHELL_NAME = 'shellName';
    const SHELL_ID = 'shellId';
    const SCENE_ID = 'sceneId';

    // 时间相关的长程维度
    const YEAR = 'year';
    const MONTH_DAY = 'monthDay';
    const MONTH = 'month';
    const WEEKDAY = 'weekday';
    const WEEK = 'week';
    const HOUR = 'hour';
    const MINUTE = 'minute';

    // 预定义的长程维度.
    const LONG_TERM_DIMENSIONS = [
        self::CLONE_ID,
        self::SENDER_ID,
        self::GUEST_ID,
        self::SHELL_NAME,
        self::SHELL_ID,
        self::YEAR,
        self::MONTH,
        self::MONTH_DAY,
        self::WEEKDAY,
        self::WEEK,
        self::HOUR,
        self::MINUTE,
    ];

    /**
     * 在当前 Scope 下, 用指定的长程维度生成一个唯一 id
     * @param string $name
     * @param string[] $longTermDimensions 维度名称.
     * @return string
     */
    public function makeScopeId(string $name, array $longTermDimensions) : string;

    /**
     * 在当前 scope, 按选择的长程维度, 获得一个维度的 map
     * @param array $longTermDimensions
     * @return array
     */
    public function getLongTermDimensionsDict(array $longTermDimensions) : array;

    /**
     * 公共的方法, 用一个维度的 Map 生成一个 Id.
     * 可以用这个方法定义自己想要的维度.
     *
     * @param string $name
     * @param string[] $dimensionsDict 自定义维度字段.
     * @return string
     */
    public function makeId(string $name, array $dimensionsDict) : string;
}