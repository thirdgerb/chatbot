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

use Commune\Blueprint\Ghost\Context\CodeContext;
use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\MindMeta\StageMeta;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\Context\Codable\AbsCodeContext;
use Commune\Ghost\Context\Codable\CodeDefCreator;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsMemoryContext extends AbsCodeContext
{
    abstract public static function __scopes() : array;

    abstract public static function __defaults() : array;


    public static function __depending(Depending $depending): Depending
    {
        $creator = new CodeDefCreator(static::class);
        $metas = $creator->getMethodStageMetas();
        $depends = array_map(
            function(StageMeta $meta){
                return [$meta->stageName, $meta->desc];
            },
            $metas
        );

        foreach ($depends as list($name, $desc)) {
            if ($name !== CodeContext::FIRST_STAGE) {
                $depending->on($name, $desc);
            }
        }

        return $depending;
    }

    public static function __option(): CodeContextOption
    {
        return new CodeContextOption([
            'memoryScopes' => static::__scopes(),
            'memoryAttrs' => static::__defaults(),
        ]);
    }

    public function __on_start(StageBuilder $stage): StageBuilder
    {
        return $stage->always($stage->dialog->fulfill());
    }

    public static function __redirect(Dialog $prev): ? Operator
    {
        return null;
    }


}