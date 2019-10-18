<?php


namespace Commune\Chatbot\OOHost\NLU\Contexts;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Callables\StageComponents\Menu;
use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\NLU\Contracts\NLUService;
use Commune\Chatbot\OOHost\NLU\NLUComponent;

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
                    IntCorpusEditor::class,
                    '同步到NLU' => 'syncToNLU',
                ]
            ))->onFallback(Redirector::goFulfill())
        );
    }

    public function __exiting(Exiting $listener): void
    {
    }


    public function __onSyncToNLU(Stage $stage) : Navigator
    {

        return $stage->onStart(function(Dialog $dialog, NLUComponent $config){

            $services = $config->nluServices;
            $speech = $dialog->say();
            $speech->beginParagraph();

            foreach($services as $service) {

                /**
                 * @var NLUService $nluService
                 */
                $nluService = $dialog->app->make($service);
                $result = $nluService->syncCorpus($dialog->session);

                if (empty($result)) {
                    $speech->info("$service : success ");

                } else {
                    $speech->error("$service : $result \n");
                }
            }
            $speech->endParagraph();

            return $dialog->restart();
        })
            ->buildTalk()
            ->goStage('start');

    }


}