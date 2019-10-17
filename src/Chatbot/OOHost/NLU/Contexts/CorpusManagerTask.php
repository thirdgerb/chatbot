<?php


namespace Commune\Chatbot\OOHost\NLU\Contexts;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Callables\StageComponents\Menu;
use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Directing\Navigator;

class CorpusManagerTask extends TaskDef
{
    const DESCRIPTION = 'nlu 语料库管理';

    const CONTEXT_TAGS = [
        Definition::TAG_MANAGER,
    ];

    public static function __depend(Depending $depending): void
    {
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->component(
            (new Menu(
                '选择功能',
                [
                    NLUMatcherTask::class,
                    IntCorpusEditor::class
                ]
            ))->onFallback(Redirector::goFulfill())
        );
    }

    public function __exiting(Exiting $listener): void
    {
    }


}