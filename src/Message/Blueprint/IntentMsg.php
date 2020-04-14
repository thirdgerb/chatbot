<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Blueprint;

use Commune\Message\Blueprint\Tag\MsgLevel;

/**
 * 对消息意图的抽象.
 * 既可以来自于用户, 也可以来自于 Ghost. 后者需要 Shell 渲染成一般消息.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface IntentMsg extends Message, MsgLevel
{
//
//    /*-------- 用户输入消息的常见意图 -------*/
//
//    ## 寻求帮助
//    const DIALOGUE_HELP = 'dialogue.help';
//    ## 表示随机
//    const DIALOGUE_RANDOM = 'dialogue.random';
//    ## 值也用 ordinal
//    const DIALOGUE_ORDINAL = 'dialogue.ordinal';
//
//    // 态度相关, 通常用于反馈
//    ## 肯定
//    const ATTITUDE_AFFIRM = 'attitude.affirm';
//    ## 同意
//    const ATTITUDE_AGREE = 'attitude.agree';
//    ## 抱怨
//    const ATTITUDE_COMPLEMENT = 'attitude.complement';
//    ## 否认
//    const ATTITUDE_DENY = 'attitude.deny';
//    ## 不允许做
//    const ATTITUDE_DONT = 'attitude.dont';
//    ## 不喜欢, 批评
//    const ATTITUDE_DISS = 'attitude.diss';
//    ## 打招呼
//    const ATTITUDE_GREET = 'attitude.greet';
//    ## 感谢
//    const ATTITUDE_THANKS = 'attitude.thanks';
//
//    // 循环相关的操作
//    const LOOP_BREAK = 'loop.break';
//    const LOOP_NEXT = 'loop.next';
//    const LOOP_PREVIOUS = 'loop.previous';
//    const LOOP_REWIND = 'loop.rewind';
//
//    // 导航相关的操作
//    const NAVIGATION_QUIT = 'navigation.quit';
//    const NAVIGATION_BACKWARD = 'navigation.backward';
//    const NAVIGATION_HOME = 'navigation.home';
//    const NAVIGATION_REPEAT = 'navigation.repeat';


    /*-------- 系统输出消息常见意图名 -------*/

    /**
     * @return string
     */
    public function getIntentName() : string;

    /**
     * 获得原始传入的 Entity 数据.
     * @return array
     */
    public function getEntities() : array;

    /**
     * 传入的单一实体数据应该是个一维数组. 每个 Entity 都可能有多个值.
     * @param string $entityName
     * @return array
     */
    public function getEntityValues(string $entityName) : array;

    /**
     * 实体认为只有单一值.
     *
     * @param string $entityName
     * @return mixed
     */
    public function getEntity(string $entityName);

}