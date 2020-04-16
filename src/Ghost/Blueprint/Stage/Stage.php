<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Stage;

use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Definition\StageDef;
use Commune\Ghost\Blueprint\Runtime\Node;
use Commune\Ghost\Blueprint\Speak\Speaker;
use Commune\Message\Blueprint\Message;


/**
 * 多轮对话管理器. 在多轮对话逻辑中, 一切都通过它来管理.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read Conversation $conversation
 * @property-read StageDef $def
 * @property-read Context $self
 * @property-read Node $node
 */
interface Stage
{
    /**
     * 用依赖注入的方式调用一个 callable.
     * 与laravel 的区别在于, $parameters 允许用 interface => $instance 的方式注入临时依赖.
     *
     * @param callable|string $caller
     * @param array $parameters
     * @return mixed
     */
    public function call($caller, array $parameters = []);

    /**
     * 开口说话.
     * @return Speaker
     */
    public function speak() : Speaker;

    /**
     * 匹配当前消息用的工具
     * @param Message|null $message
     * @return Matcher
     */
    public function matcher(Message $message = null) : Matcher;
}