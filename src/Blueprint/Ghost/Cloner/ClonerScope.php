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

use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 当前 Ghost 分身的作用域. 可以用于定义记忆体的长程作用域.
 *
 * ## 系统默认长程维度
 *
 * @property-read string $shellId
 * @property-read string $sessionId         Clone 的 Id.
 * @property-read string $convoId
 * @property-read string $guestId           消息发送者在 Clone 里的 GuestId, 平台无关.
 *
 * ## 时间相关维度
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
interface ClonerScope extends ArrayAndJsonAble
{
    // 长程维度.
    const SESSION_ID = 'sessionId';
    const SHELL_ID = 'shellId';
    const CONVO_ID = 'convoId';
    const GUEST_ID = 'guestId';

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
        self::SHELL_ID,
        self::SESSION_ID,
        self::GUEST_ID,
        self::CONVO_ID,

        ## 时间维度
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