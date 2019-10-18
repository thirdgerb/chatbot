<?php


namespace Commune\Components\SimpleChat\Providers;


use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\Contracts\ConsoleLogger;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\Context\Intent\PlaceHolderIntentDef;
use Commune\Chatbot\OOHost\NLU\Options\IntentCorpusOption;
use Commune\Components\SimpleChat\Options\ChatOption;
use Commune\Support\OptionRepo\Contracts\OptionRepository;

class RegisterSimpleChat extends ServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

    public function boot($app)
    {
        /**
         * @var OptionRepository $repo
         * @var IntentRegistrar $intRepo
         * @var ChatOption $option
         * @var ConsoleLogger $logger
         */
        $repo = $app[OptionRepository::class];
        $intRepo = $app[IntentRegistrar::class];
        $logger = $app[ConsoleLogger::class];

        foreach ($repo->eachOption(ChatOption::class) as $option) {
            $name = $option->intent;

            // 注册尚不存在的意图.
            if (!$intRepo->hasDef($name)) {
                $intRepo->registerDef(new PlaceHolderIntentDef($name), false);
                $logger->debug('simple chat register placeHolderIntent [' . $name .']');
            }

            // 注册尚不存在的例句.
            $chatExamples = $option->examples;
            if (empty($chatExamples)) {
                continue;
            }

            $corpusOption = $repo->has(IntentCorpusOption::class, $name)
                ? $repo->find(IntentCorpusOption::class, $name)
                : new IntentCorpusOption([
                    'name' => $name
                ]);


            $examples = $corpusOption->examples;
            $toSave = [];
            if (empty($examples)) {
                $corpusOption->mergeExamples($option->examples);
                $logger->debug('simple chat register examples for ' . $name);
                $toSave[] = $corpusOption;
            }

            // 同步.
            if (!empty($toSave)) {
                $repo->saveBatch(IntentCorpusOption::class, false, ...$toSave);
            }
        }


    }

    public function register()
    {
    }


}