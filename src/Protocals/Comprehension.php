<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals;

use Commune\Protocals\Abstracted\Choice;
use Commune\Protocals\Abstracted\Command;
use Commune\Protocals\Abstracted\Confirmation;
use Commune\Protocals\Abstracted\Emotion;
use Commune\Protocals\Abstracted\Entity;
use Commune\Protocals\Abstracted\Intention;
use Commune\Protocals\Abstracted\Question;
use Commune\Protocals\Abstracted\Reply;
use Commune\Protocals\Abstracted\Selection;
use Commune\Protocals\Abstracted\SoundLike;
use Commune\Protocals\Abstracted\Tags;
use Commune\Protocals\Abstracted\Vector;
use Commune\Support\Message\Message;
use Commune\Support\Message\Protocal;

/**
 * 对输入消息的抽象理解.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * # 协议的部分.
 *
 * @property-read Intention     $intention      意图理解模块
 * @property-read Entity        $entity         公共的实体识别
 *
 * @property-read Vector        $vector         向量模块, 可以本地计算近似度
 *
 * @property-read Command       $command        命令模块, 检查是否是命令.
 *
 * @property-read Tags          $tags           标签模块, 可以匹配标签关键字.
 * @property-read SoundLike     $soundLike      语音模块, 用于弥补近似发音识别错误的问题, 也可以在特定场景下加快识别效率.
 *
 *
 * @property-read Question      $question       认为输入是个问题
 * @property-read Choice        $choice         认为输入是个单向选择
 * @property-read Confirmation  $confirmation   认为输入是个确认信息
 * @property-read Selection     $selection      认为输入是一个多项选择.
 *
 * @property-read Reply         $reply          回复模块, 如果第三方 API 能给出答案.
 *
 * @property-read Emotion       $emotion        情绪模块, 从各种模块中得到的综合抽象, 可以代表多种模块
 *
 * @property-read bool[]        $typeHandled    各种类型的处理结果
 * @property-read string[]      $typeHandledBy  各种类型被处理的对象.
 */
interface Comprehension extends Message, Protocal
{


    /**
     * 标记已经使用某种类型的理解工具进行操作.
     *
     * @param string $type
     * @param string $comprehenderId
     * @param bool $succeed
     */
    public function handledBy(string $type, string $comprehenderId, bool $succeed) : void;

    /**
     * 是否被某个组件操作过了.
     * @param string $type
     * @return bool
     */
    public function isHandledBy(string $type) : bool;

    /**
     * 是否被某个组件操作过, 而且还成功了.
     *
     * @param string $type
     * @return bool
     */
    public function isSucceedBy(string $type) : bool;
}