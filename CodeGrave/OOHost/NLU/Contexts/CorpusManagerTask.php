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
use Commune\Chatbot\OOHost\NLU\Contracts\Corpus;
use Commune\Chatbot\OOHost\NLU\Contracts\NLUService;
use Commune\Chatbot\OOHost\NLU\NLUComponent;
use Commune\Chatbot\OOHost\NLU\Options\EntityDictOption;

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
        return $stage
            ->onFallback(Redirector::goFulfill())
            ->component(
                (new Menu(
                    '选择功能',
                    [
                        NLUMatcherTask::class,
                        '同步语料库到NLU' => [$this, 'syncToNLU'],
                        '同步语料库到本地存储' => [$this, 'syncToLocal'],
                        IntCorpusEditor::class,
                    ]
                ))
            );
    }

    public function __exiting(Exiting $listener): void
    {
    }


    public function syncToNLU(Dialog $dialog, NLUComponent $config) : Navigator
    {
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
    }

    public function syncToLocal(Dialog $dialog, Corpus $corpus) : Navigator
    {
        $output = $corpus->sync(true);

        $manager = $corpus->entityDictManager();
        $manager->save(new EntityDictOption(['name' => 'test']));

        if (empty($output)) {
            $dialog->say()->info('sync success');
        } else {
            $dialog->say()->error("error occur : $output");
        }

        return $dialog->restart();
    }


}