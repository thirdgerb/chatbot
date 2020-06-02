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

use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Ghost\Context\Codable\AbsCodeContext;
use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Ghost\Support\ContextUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ACodeContext extends AbsCodeContext
{

    public static function __name() : string
    {
        return ContextUtils::normalizeContextName(static::class);
    }

    abstract public static function __depending(Depending $depending): Depending;

    abstract public static function __option(): CodeContextOption;

    abstract public function __on_start(StageBuilder $builder): StageBuilder;



}