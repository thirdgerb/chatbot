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
use Commune\Components\Markdown\Constants\MDContextLang;
use Commune\Components\Markdown\Mindset\SectionStageDef;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class AskChooseAls extends AbsAwaitAnalyser
{
    public function __invoke(
        Dialog $dialog,
        SectionStageDef $def,
        string $content,
        Await $await
    ): ? Operator
    {
        $query = empty($content)
            ? MDContextLang::ASK_CHOOSE
            : $content;

        return $dialog
            ->await()
            ->askChoose($query);
    }


}