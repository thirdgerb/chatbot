<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Blueprint\Directive;

use Commune\Message\Blueprint\Message;

/**
 * 通常是 Ghost 给 Shell 下达的命令.
 * 应当立刻执行.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface DirectiveMsg extends Message
{
    //常用系统命令

    # 退出会话
    const QUIT_SESSION = 'directive.quitSession';

    public function getId() : string;

    public function getPayload() : array;

}