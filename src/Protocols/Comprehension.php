<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocols;

use Commune\Protocols\Abstracted;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * 对输入消息的抽象理解.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * # 协议的部分.
 *
 * @property-read Abstracted\Answer|null    $answer         回答
 * @property-read Abstracted\Cmd            $command        命令模块, 检查是否是命令.
 * @property-read Abstracted\Emotion        $emotion        情绪模块, 从各种模块中得到的综合抽象, 可以代表多种模块
 *
 * @property-read Abstracted\Intention      $intention      意图理解模块
 *
 * @property-read Abstracted\Replies        $replies        回复模块, 如果第三方 API 能给出答案.
 * @property-read Abstracted\Tokenize       $tokenize         分词, 这一步还不完善, 可能要做
 *
 * @property-read Abstracted\Routing        $routing        设置重定向的目的地.
 */
interface Comprehension extends ArrayAndJsonAble
{
    const TYPE_ANSWER = 'answer';
    const TYPE_COMMAND = 'command';
    const TYPE_EMOTION = 'emotion';
    const TYPE_INTENTION = 'intent';
    const TYPE_REPLIES = 'replies';
    const TYPE_TOKENIZE = 'tokenize';
    const TYPE_ROUTING = 'routing';

    /**
     * 表示某个功能已经被处理过了. 这在调用多个 NLU 时可以防止重复调用.
     * 多个 NLU 通常是串行处理. 如果需要并行, 应该合并到一个 NLUService 中.
     *
     * @param string $type
     * @param string $comprehenderId
     * @param bool $success
     */
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
