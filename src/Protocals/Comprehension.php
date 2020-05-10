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

use Commune\Support\Message\Message;

/**
 * 对输入消息的抽象理解.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * # 协议的部分.
 *
 * @property-read Abstracted\Intention     $intention      意图理解模块
 * @property-read Abstracted\Entity        $entity         公共的实体识别
 *
 * @property-read Abstracted\Vector        $vector         向量模块, 可以本地计算近似度
 *
 * @property-read Abstracted\Command       $command        命令模块, 检查是否是命令.
 *
 * @property-read Abstracted\Tags          $tags           标签模块, 可以匹配标签关键字.
 * @property-read Abstracted\SoundLike     $soundLike      语音模块, 用于弥补近似发音识别错误的问题, 也可以在特定场景下加快识别效率.
 *
 *
 * @property-read Abstracted\Question      $question       认为输入是个问题
 * @property-read Abstracted\Choice        $choice         认为输入是个单向选择
 * @property-read Abstracted\Selection     $selection      认为输入是一个多项选择.
 *
 * @property-read Abstracted\Reply         $reply          回复模块, 如果第三方 API 能给出答案.
 *
 * @property-read Abstracted\Emotion       $emotion        情绪模块, 从各种模块中得到的综合抽象, 可以代表多种模块
 *
 * @property-read bool[]        $typeHandled    各种类型的处理结果
 * @property-read string[]      $typeHandledBy  各种类型被处理的对象.
 */
interface Comprehension extends Message
{
    /**
     * 标记已经使用某种类型的理解工具进行操作.
     *
     * @param string $comprehenderId
     * @param bool $succeed
     */
    public function handledBy(string $comprehenderId, bool $succeed) : void;

    /**
     * 是否被某个组件操作过了.
     * @param string $comprehenderId
     * @return bool
     */
    public function isHandledBy(string $comprehenderId) : bool;

    /**
     * 是否被某个组件操作过, 而且还成功了.
     *
     * @param string $comprehenderId
     * @return bool
     */
    public function isSucceedBy(string $comprehenderId) : bool;
}