<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocols\HostMsg\Directive;

use Commune\Blueprint\Ghost\Cloner;


/**
 * 指令的定义. 通常由客户端上传给服务端.
 * 反向也能实现, 暂时不作为本系统的默认方案
 * (本系统目前仍然以对话为主, 所以 Conversational::$suggestions 是主要交互形式)
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name              命令的名称.
 *
 * @property-read string[] $verbals         口头触发指令的规则, 允许使用通配符 * 不允许用正则. 因为跨语言的正则转义太麻烦了, 以后再说吧.
 *
 */
interface DirectiveDef
{

    /**
     * 通过 def 直接判断是否命中了某个命令.
     * 定义这个方法, 也是为了让服务端有可能预定义自己的指令集.
     *
     * @param Cloner $cloner
     * @return DirectiveMsg|null
     */
    public function match(Cloner $cloner) : ? DirectiveMsg;
}