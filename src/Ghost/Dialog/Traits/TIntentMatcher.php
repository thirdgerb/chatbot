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
use Commune\Ghost\Dialog\AbsDialog;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 * @mixin AbsDialog
 */
trait TIntentMatcher
{

    protected function matchStageRoutes(Ucl $current, array $stages = []) : ? Ucl
    {
        $matcher = $this->_cloner->matcher->refresh();
        foreach ($stages as $stage) {
            $intentName = $current->getStageIntentName($stage);
            if ($matcher->matchStageOfIntent($intentName)->truly()) {
                return $current->goStage($stage);
            }
        }

        return null;
    }

    protected function matchContextRoutes(Ucl ...$contexts) : ? Ucl
    {
        $matcher = $this->cloner->matcher->refresh();

        foreach ($contexts as $ucl) {
            $intentName = $ucl->getStageIntentName();
            if ($matcher->matchStageOfIntent($intentName)->truly()) {
                return $ucl->goStageByIntentName($intentName);
            }
        }

        return null;
    }
}