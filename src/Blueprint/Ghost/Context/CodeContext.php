<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Context;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\MindSelfRegister;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\MindMeta\ContextMeta;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Support\Option\Wrapper;


/**
 * 可以在方法名和类名上使用的注解类型.
 *
 * @title title
 * @desc desc
 * @intent intentNameAlias
 * @example intentExample1
 * @example intentExample2
 * @regex /^regex1$/
 * @regex /^regex2$/
 * @signature commandName {arg1} {arg2}
 *
 */


/**
 * 用代码来定义的 Context, 基于面向对象, 将 Stage 的方法都定义在 Context 内部.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface CodeContext extends Context, Wrapper, MindSelfRegister
{

    const FIRST_STAGE = 'start';

    const FUNC_REDIRECT = '__redirect';
    const FUNC_DEFINE_DEPENDING = '__depending';
    const FUNC_MAKE_DEF = '__def';
    const FUNC_CONTEXT_NAME = '__name';
    const FUNC_CONTEXT_OPTION = '__option';

    const STAGE_BUILDER_PREFIX = '__on_';

    public static function makeUcl(array $query = [], string $stage = '') : Ucl;

    /**
     * 定义 context name
     * @return string
     */
    public static function __name() : string;

    /**
     * 定义启动时必须要设置值的参数.
     *
     * @param Depending $depending
     * @return Depending
     */
    public static function __depending(Depending $depending) : Depending;

    /**
     * @param ContextMeta|null $meta
     * @return ContextDef
     */
    public static function __def(ContextMeta $meta = null) : ContextDef;

    /**
     * @return CodeContextOption
     *  return new CodeContextOption($option = []);
     */
    public static function __option() : CodeContextOption;

    /**
     * 重定向到当前 context 时.
     * @param Dialog $prev
     * @return Operator|null
     */
    public static function __redirect(Dialog $prev) : ? Operator;

    /**
     * @param StageBuilder $stage
     * @return StageBuilder
     */
    public function __on_start(StageBuilder $stage) : StageBuilder;
}