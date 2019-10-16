<?php


namespace Commune\Chatbot\Framework\Component\Providers;


use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\NLU\Options\IntentCorpusOption;
use Commune\Support\OptionRepo\Contracts\OptionRepository;
use Symfony\Component\Yaml\Yaml;

/**
 * 加载 nlu 的意图语料. 作为 corpus 的补充.
 */
class LoadNLUExamplesFromYaml extends ServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

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
        $options = Yaml::parse($data);

        if (empty($options)) {
            return;
        }

        if (!is_array($options)) {
            throw new ConfigureException(
                __METHOD__
                .' nlu examples resource '
                . $resource
                . ' invalid, json expected'
            );
        }


        /**
         * @var OptionRepository $repo
         */
        $repo = $app[OptionRepository::class];

        $toSave = [];
        foreach ($options as $example) {
            $intentOption = new IntentCorpusOption($example);
            if (!$repo->has( IntentCorpusOption::class, $intentOption->getId())) {
                $toSave[] = $intentOption;
            }
        }

        if (!empty($toSave)) {
            $repo->saveBatch( IntentCorpusOption::class, true,  ...$toSave);
        }

    }

    public function register()
    {
    }


}