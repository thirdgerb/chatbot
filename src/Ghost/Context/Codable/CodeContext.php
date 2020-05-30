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

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\MindSelfRegister;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\MindMeta\ContextMeta;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Support\Option\Wrapper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface CodeContext extends Context, Wrapper, MindSelfRegister
{

    const MAKE_DEF_FUNC = 'makeDef';
    const WRAP_CONTEXT_FUNC = 'wrapContext';
    const FIRST_STAGE = 'start';


    /**
     * @param Ucl $ucl
     * @param Cloner $cloner
     * @return static
     */
    public static function wrapContext(Cloner $cloner, Ucl $ucl) : Context;

    public static function getContextName() : string;

    public static function makeDef(ContextMeta $meta = null) : ContextDef;

    /**
     * @param StageBuilder $builder
     * @return StageBuilder
     */
    public static function __on_start(StageBuilder $builder) : StageBuilder;
}