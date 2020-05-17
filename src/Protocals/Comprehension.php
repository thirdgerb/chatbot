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

use Commune\Protocals\Abstracted;

/**
 * 对输入消息的抽象理解.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * # 协议的部分.
 *
 * @property-read Abstracted\Choice         $choice         认为输入是个单向选择
 * @property-read Abstracted\Cmd            $command        命令模块, 检查是否是命令.
 * @property-read Abstracted\Emotion        $emotion        情绪模块, 从各种模块中得到的综合抽象, 可以代表多种模块
 *
 * @property-read Abstracted\Intention      $intention      意图理解模块
 * @property-read Abstracted\Query          $query       认为输入是个问题
 *
 *
 * @property-read Abstracted\Replies        $replies        回复模块, 如果第三方 API 能给出答案.
 * @property-read Abstracted\Selection      $selection      认为输入是一个多项选择.
 *
 *
 * @property-read Abstracted\Tokens         $tokens
 * @property-read Abstracted\Vector         $vector
 *
 */
interface Comprehension
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