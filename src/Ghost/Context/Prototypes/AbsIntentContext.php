<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context\Prototypes;

use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\Context\Codable\AbsCodeContext;

/**
 * 由意图触发的语境, 相当于事件响应, 没有复杂多轮对话流程.
 * 主要扮演拦截器的角色.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsIntentContext extends AbsCodeContext
{

    public static function __depending(Depending $depending): Depending
    {
        // 没有属性定义.
        return $depending;
    }

    public static function __option(): CodeContextOption
    {
        // 没有逻辑定义.
        return new CodeContextOption([]);
    }

    // 直接执行 action.
    public function __on_start(StageBuilder $stage): StageBuilder
    {
        return $stage->always($stage->dialog->fulfill());
    }

}