<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context;

use Commune\Blueprint\Ghost\Context\CodeContext;
use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\Context\Codable\AbsCodeContext;
use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Ghost\Support\ContextUtils;


/**
 * 标准的用  代码 + 注解 来实现的上下文语境.
 *
 * 比起配置来更加灵活, 更易于编程. 也更加直观.
 *
 * @see CodeContext
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ACodeContext extends AbsCodeContext
{

    /**
     * Stage 注解: 将以下注解用于类名上, 可以定义 Context 的相关属性, 主要是意图相关的属性
     * 也可以用于 __on_stage_name 的方法上, 定义 Stage 意图相关的属性.
     *
     * @title 标题, 唯一
     * @desc 简介, 唯一
     * @intent 意图的别名, 唯一
     * @spell 可以精确命中意图的字符串. 唯一
     * @example 意图的语料, 用 [实体名](实体值) 来标注实体
     * @entity 用来定义意图的实体参数, 数组类型的参数要加 "[]"作为后缀. 例如 "city", 或者 "date[]"
     * @signature 用来定义命令. 唯一
     */

    /**
     * 定义语境的名称.
     * @return string
     */
    public static function __name() : string
    {
        return ContextUtils::normalizeContextName(static::class);
    }

    /**
     * 定义 Context 的基础属性.
     *
     * return new CodeContextOption([]). 详细配置见该类.
     *
     * @return CodeContextOption
     */
    abstract public static function __option(): CodeContextOption;

    /**
     * 定义上下文语境的默认参数. 只有这些参数都 !null 时, context 才会继续往后走.
     *
     * $context->isPrepared() 表示相关参数已经有值.
     * $context->depending() 则得到仍然需要赋值的参数名i.
     *
     * @param Depending $depending
     * @return Depending
     */
    abstract public static function __depending(Depending $depending): Depending;

    /**
     * 当另一个 Stage 试图进入当前 Context 时, 允许拦截掉该请求, 并给出别的重定向逻辑.
     *
     * @param Dialog $prev
     * @return Operator|null
     */
    public static function __redirect(Dialog $prev): ? Operator
    {
        return null;
    }

    /**
     * 所有 CodeContext 的默认第一个 stage.
     *
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    abstract public function __on_start(StageBuilder $stage): StageBuilder;


    // 用 __on_ 作为前缀, 可以自定义 Stage. 并允许用注解来定义 stage 的默认参数.
    // public function __on_self_defined_stage_name(StageBuilder $stage) : StageBuilder;
}