<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\Traits;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Definition\StageDef;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
trait TIntentMatcher
{
    /**
     * 使用了 通配符的情况, 在 Comprehension 中寻找可能的 intent 名称对应的 Stage
     * @param string $intent
     * @param Cloner $cloner
     * @return StageDef|null
     */
    protected function wildcardIntentMatch(string $intent, Cloner $cloner) : ? StageDef
    {
        $intention = $cloner->ghostInput->comprehension->intention;

        // 不是模糊搜索就退出.
        if (!$intention->isWildcardIntent($intent)) {
            return null;
        }

        //
        $matched = $intention->wildcardIntentMatch($intent);
        if (empty($matched)) {
            return null;
        }

        $stageReg = $cloner->mind->stageReg();
        foreach ($matched as $intentName) {
            if ($stageReg->hasDef($intentName)) {
                return $stageReg->getDef($intentName);
            }
        }

        return null;
    }

    /**
     * @param string $intent
     * @param Cloner $cloner
     * @return StageDef|null
     */
    protected function exactIntentMatch(string $intent, Cloner $cloner) : ? StageDef
    {
        $intentReg = $cloner->mind->intentReg();

        if (! $intentReg->hasDef($intent)) {
            return null;
        }

        // 用意图单元来匹配.
        $intentDef = $intentReg->getDef($intent);
        $matched = $intentDef->validate($cloner);

        if ($matched) {
            $cloner
                ->ghostInput
                ->comprehension
                ->intention
                ->setMatchedIntent($intent);
        }

        return $matched ? $intent : null;
    }


}