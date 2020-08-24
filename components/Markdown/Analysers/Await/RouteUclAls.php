<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Markdown\Analysers\Await;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Await;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Components\Markdown\Mindset\SectionStageDef;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RouteUclAls extends AbsAwaitAnalyser
{
    public function __invoke(
        Dialog $dialog,
        SectionStageDef $def,
        string $content,
        Await $await
    ): ? Operator
    {

        list($uclStr, $index) = $this->separateRouteAndIndex($content);

        $cloner = $dialog->cloner;
        $ucl = Ucl::decode($uclStr);
        if (!$ucl->isValidPattern()) {
            $cloner->logger->error(
                __METHOD__
                . " ucl [$content] is invalid"
            );
            return $await;
        }

        if (!$ucl->stageExists($cloner)) {
            $stageName = $ucl->getStageFullname();
            $cloner->logger->error(
                __METHOD__
                . " stage $stageName not exists "
            );
            return $await;
        }

        $question = $await->getCurrentQuestion();

        list ($index, $suggestion) = $this->parseIndexAndDesc($ucl, $cloner, $index);
        $question->addSuggestion($suggestion, $index, $ucl);
        return $await;
    }


}