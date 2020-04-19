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
     * 在 Stage 的上下文中通过抽象获取一个对象.
     *
     * @param string $abstract
     * @param array $parameters
     * @return mixed
     */
    public function make(string $abstract, array $parameters = []);

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
     * 获取上下文相关的依赖注入对象.
     * Stage::call , Stage::make 方法都会注入这些对象.
     * @return array
     */
    public function getContextInjections() : array;

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