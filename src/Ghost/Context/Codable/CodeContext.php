<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context\Codable;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\MindSelfRegister;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\MindMeta\ContextMeta;
use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Support\Option\Wrapper;


/**
 * 意图默认值定义的规则, 采取注解的形式.
 * 可以用于类的注解, 以及 stage 方法的注解.
 *
 * @intent intentNameAlias
 * @example intentExample1
 * @example intentExample2
 * @regex /^regex1$/
 * @regex /^regex2$/
 * @signature commandName {arg1} {arg2}
 *
 */

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface CodeContext extends Context, Wrapper, MindSelfRegister
{

    const FIRST_STAGE = 'start';

    const MAKE_DEF_FUNC = '__def';
    const CONTEXT_NAME_FUNC = '__name';
    const CONTEXT_OPTION_FUNC = '__option';
    const STAGE_BUILDER_PREFIX = '__on_';


    /**
     * @return string
     */
    public static function __name() : string;

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
     * @param StageBuilder $builder
     * @return StageBuilder
     */
    public static function __on_start(StageBuilder $builder) : StageBuilder;
}