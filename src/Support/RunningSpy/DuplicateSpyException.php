<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\RunningSpy;


/**
 * RunningSpy 监视的实例应该是全局唯一的.
 * 如果出现了重复, 则一定有逻辑上的错误 (或者全局ID 不唯一. 都需要检查).
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class DuplicateSpyException extends \LogicException
{
    public function __construct(string $traceId, string $spied)
    {
        $message = "duplicate spy $spied occur, trace id is $traceId";
        parent::__construct($message);
    }

}