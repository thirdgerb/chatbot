<?php


namespace Commune\Chatbot\Framework\Component\Providers;


use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\OOHost\NLU\Options\EntityDictOption;
use Commune\Support\OptionRepo\Contracts\OptionRepository;
use Symfony\Component\Yaml\Yaml;

/**
 * 加载 nlu 的实体词典. 作为 corpus 的补充.
 */
class RegisterOptionFromYaml extends ServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

    /**
     * @var string
     */
    protected $resource;

    protected $category;

    protected $optionClazz;

    protected $force;

    public function __construct(
        $app,
        string $resource,
        string $category,
        string $optionClazz,
        bool $force
    )
    {
        $this->category = $category;
        $this->optionClazz = $optionClazz;
        $this->resource = $resource;
        $this->force = $force;
        parent::__construct($app);
    }

    public function boot($app)
    {
        $resource = $this->resource;
        if (!file_exists($resource)) {
            throw new ConfigureException(
                __METHOD__
                . $this->category . '  resource '
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
            $clazz = $this->optionClazz;
            $option = new $clazz($example);
            $id = $option->getId();
            if (empty($id)) {
                continue;
            }

            if ($this->force || !$repo->has($this->category, $id)) {
                $toSave[] = $option;
            }
        }

        if (!empty($toSave)) {
            $repo->saveBatch($this->category, true, ...$toSave);
        }

    }

    public function register()
    {
    }


}