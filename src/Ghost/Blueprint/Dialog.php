<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint;

use Commune\Framework\Blueprint\ReqContainer;
use Commune\Ghost\Blueprint\Context\Context;
use Commune\Ghost\Blueprint\Session\Scene;
use Commune\Ghost\Blueprint\Memory\Memory;
use Commune\Ghost\Blueprint\Meta\MetaRegistrar;
use Commune\Ghost\Blueprint\Definition\Mindset;
use Commune\Ghost\Blueprint\Runtime\Route;
use Commune\Ghost\Blueprint\Runtime\Runtime;
use Commune\Ghost\Blueprint\Speak\Speaker;
use Commune\Message\Blueprint\Abstracted\Comprehension;
use Commune\Message\Blueprint\ConvoMsg;
use Commune\Message\Blueprint\Directive\DirectiveMsg;
use Commune\Message\Blueprint\Internal\InputMsg;
use Commune\Message\Blueprint\Message;
use Commune\Message\Blueprint\Reaction\ReactionMsg;
use Commune\Shell\Blueprint\Session\ShlSession;


/**
 * 多轮对话管理器. 在多轮对话逻辑中, 一切都通过它来管理.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 以下组件都可以依赖注入
 *
 *
 * @property-read ShlSession $session                   Session
 *
 * 语境相关
 * @property-read Context $context                      上下文语境
 * @property-read Runtime $runtime                      多轮对话逻辑状态
 * @property-read Route $route
 *
 * @property-read Speaker $speaker
 *
 */
interface Dialog
{
    /**
     * Resolve the given type from the container.
     *
     * @param  string  $abstract
     * @param  array  $parameters
     * @return mixed
     */
    public function make(string $abstract, array $parameters = []);

    /**
     * 用依赖注入的方式调用一个 callable.
     * 与laravel 的区别在于, $parameters 允许用 interface => $instance 的方式注入临时依赖.
     *
     * @param callable $caller
     * @param array $parameters
     * @return mixed
     */
    public function call(callable $caller, array $parameters = []);


}