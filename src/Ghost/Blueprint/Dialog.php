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
use Commune\Ghost\Blueprint\Context\Scene;
use Commune\Ghost\Blueprint\Memory\Memory;
use Commune\Ghost\Blueprint\Meta\MetaRegistrar;
use Commune\Ghost\Blueprint\Mind\Mindset;
use Commune\Ghost\Blueprint\Runtime\Runtime;
use Commune\Ghost\Blueprint\Speak\Speaker;
use Commune\Message\Abstracted\Comprehension;
use Commune\Message\Convo\ConvoMsg;
use Commune\Message\Directive\DirectiveMsg;
use Commune\Message\Internal\IncomingMsg;
use Commune\Message\Message;
use Commune\Message\Reaction\ReactionMsg;
use Commune\Shell\Blueprint\Session\ShlSession;


/**
 * 多轮对话管理器. 在多轮对话逻辑中, 一切都通过它来管理.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 以下组件都可以依赖注入
 *
 * # 容器
 * @property-read ReqContainer $container               请求级容器
 *
 * # 外部
 * @property-read ShlSession $session                   Session
 * @property-read Ghost $ghost                          Ghost
 *
 * # 消息相关
 * @property-read ConvoMsg $message                     请求消息
 * @property-read IncomingMsg $incoming                 请求消息的封装, 包含了 Scope
 * @property-read Comprehension $comprehension          请求的高级抽象
 * @property-read Scene $scene                          场景信息的封装.
 *
 * # 语境相关
 * @property-read Context $context                      上下文语境
 * @property-read Runtime $runtime                      多轮对话逻辑状态
 *
 * # 多轮对话相关
 * @property-read Speaker $speaker
 * @property-read Mindset $mind
 * @property-read MetaRegistrar $metaReg
 * @property-read Memory $memory
 */
interface Dialog
{

}