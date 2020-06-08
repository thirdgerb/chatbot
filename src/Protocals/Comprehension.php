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
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * 对输入消息的抽象理解.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * # 协议的部分.
 *
 * @property-read Abstracted\Answer         $answer         回答
 * @property-read Abstracted\Cmd            $command        命令模块, 检查是否是命令.
 * @property-read Abstracted\Emotion        $emotion        情绪模块, 从各种模块中得到的综合抽象, 可以代表多种模块
 *
 * @property-read Abstracted\Intention      $intention      意图理解模块
 * @property-read Abstracted\Query          $query          认为用户输入是个问题
 *
 *
 * @property-read Abstracted\Replies        $replies        回复模块, 如果第三方 API 能给出答案.
 * @property-read Abstracted\Selection      $selection      认为输入是一个多项选择.
 *
 *
 * @property-read Abstracted\Tokenize       $tokens
 * @property-read Abstracted\Vector         $vector
 *
 */
interface Comprehension extends ArrayAndJsonAble
{
    const TYPE_ANSWER = 'answer';
    const TYPE_COMMAND = 'command';
    const TYPE_EMOTION = 'emotion';
    const TYPE_INTENTION = 'intent';
    const TYPE_QUERY = 'query';
    const TYPE_REPLIES = 'replies';
    const TYPE_SELECTION = 'selection';
    const TYPE_TOKENIZE = 'tokenize';
    const TYPE_VECTOR = 'vector';

    public function handled(
        string $type,
        string $comprehenderId,
        bool $success
    ) : void;

    /**
     * @param string $type
     * @return bool
     */
    public function isHandled(string $type) : bool;

    /**
     * @param string $comprehenderId
     * @param string|null $type
     * @return bool
     */
    public function isHandedBy(
        string $comprehenderId,
        string $type = null
    ) : bool;

    public function isSucceed(
        string $type,
        string $comprehenderId = null
    ) : bool;
}
