<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Predefined\Intent\Navigation;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\Context\AIntentContext;
use Commune\Protocols\HostMsg\DefaultIntents;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @title 重启语境
 * @desc 当前语境从头开始对话
 *
 * @example 重来一次
 * @example 重来一遍吧
 * @example 从头开始
 * @example 重新来一次
 * @example 从第一步再来
 * @example 能不能重新开始
 * @example 我想要重来一遍
 */
class RestartInt extends AIntentContext
{

    public static function __name(): string
    {
        return DefaultIntents::GUEST_NAVIGATE_RESTART;
    }

    public static function __redirect(Dialog $prev): Operator
    {
        return $prev->goStage('');
    }

}