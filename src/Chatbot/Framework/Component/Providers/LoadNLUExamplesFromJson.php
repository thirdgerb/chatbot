<?php


namespace Commune\Chatbot\Framework\Component\Providers;


use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrarImpl;
use Commune\Chatbot\OOHost\Context\Intent\PlaceHolderIntentDef;
use Commune\Chatbot\OOHost\NLU\Corpus\Example as NLUExample;

class LoadNLUExamplesFromJson extends ServiceProvider
{
    /**
     * @var string
     */
    protected $resource;

    public function __construct($app, string $resource)
    {
        $this->resource = $resource;
        parent::__construct($app);
    }

    public function boot($app)
    {

        $resource = $this->resource;
        if (!file_exists($resource)) {
            throw new ConfigureException(
                __METHOD__
                .' nlu examples resource '
                . $resource
                . ' not exists, json file expected'
            );
        }

        $data = file_get_contents($resource);
        $json = json_decode($data, true);

        if (!is_array($json)) {
            throw new ConfigureException(
                __METHOD__
                .' nlu examples resource '
                . $resource
                . ' invalid, json expected'
            );
        }

        /**
         * @var IntentRegistrar $repo
         */
        $repo = $app->get(IntentRegistrar::class);

        foreach ($json as $intentName => $examples) {
            if (!is_array($examples)) {
                throw new ConfigureException(
                    __METHOD__
                    . ' intent examples of '
                    . $intentName
                    . ' is invalid format'
                );
            }
            $nluExamples = [];
            foreach ($examples as $example) {
                $nluExamples[]= new NLUExample($example);
            }

            // 生成占位符
            if (!$repo->hasDef($intentName)) {
                $repo->registerDef(new PlaceHolderIntentDef($intentName), false);
            }

            $repo->setIntentNLUExamples($intentName, $nluExamples);
        }

    }

    public function register()
    {
    }


}