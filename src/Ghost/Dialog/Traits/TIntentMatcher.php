<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog\Traits;

use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsDialogue;
use Commune\Support\Utils\StringUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 * @mixin AbsDialogue
 */
trait TIntentMatcher
{

    protected function exactStageIntentMatch(Ucl $ucl) : bool
    {
        $intentDef = $ucl->findIntentDef($this->cloner);

        return !empty($intentDef)
            ? $intentDef->validate($this->cloner)
            : false;
    }

    protected function wildCardIntentNameMatch(string $name) : ? array
    {
        $intention = $this->cloner->ghostInput->comprehension->intention;
        return $intention->wildcardIntentMatch($name);
    }


    protected function checkMatchedStageNameExists(
        Ucl $current,
        array $matched,
        bool $sameContext =false
    ) : ? string
    {
        $reg = $this->cloner->mind->stageReg();
        foreach ($matched as $stageName) {
            $ifSameContext = !$sameContext || $current->isSameContext($stageName);
            if ($ifSameContext && $reg->hasDef($stageName)) {
                return $stageName;
            }
        }

        return null;
    }

    /**
     * @param Ucl $current
     * @param string[] $stages
     * @return Ucl|null
     */
    protected function stageRoutesMatch(Ucl $current, array $stages) : ? Ucl
    {
        foreach ($stages as $stageName) {

            if (StringUtils::isWildCardPattern($stageName)) {
                $wildCardStageIntentName = $current->parseFullStageName($stageName);

                // 只检查意图名是否正确.
                $matched = $this->wildCardIntentNameMatch($wildCardStageIntentName);
                if (empty($matched)) {
                    continue;
                }

                $matchedStage = $this->checkMatchedStageNameExists(
                    $current,
                    $matched,
                    true
                );

                if (isset($matchedStage)) {
                    return $current->gotoFullnameStage($matchedStage);
                }

            } else {
                // fullname
                $stageUcl = $current->gotoStage($stageName);
                if ($this->exactStageIntentMatch($stageUcl)) {
                    return $stageUcl;
                }
            }
        }

        return null;
    }

}