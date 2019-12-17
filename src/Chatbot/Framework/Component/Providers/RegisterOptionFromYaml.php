<?php


namespace Commune\Chatbot\Framework\Component\Providers;


use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Chatbot\Contracts\ConsoleLogger;
use Commune\Chatbot\Framework\Exceptions\ChatbotLogicException;
use Commune\Container\ContainerContract;
use Commune\Support\OptionRepo\Contracts\OptionRepository;
use Symfony\Component\Yaml\Yaml;

class RegisterOptionFromYaml extends ServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

    /**
     * @var string
     */
    protected $resource;

    /**
     * @var string
     */
    protected $category;

    /**
     * @var string
     */
    protected $optionClazz;

    /**
     * @var bool
     */
    protected $force;

    /**
     * RegisterOptionFromYaml constructor.
     * @param ContainerContract $app
     * @param string $resource
     * @param string $category
     * @param string $optionClazz
     * @param bool $force
     */
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
            throw new ChatbotLogicException(
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
            throw new ChatbotLogicException(
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
        $logger = $app[ConsoleLogger::class];


        $toSave = [];
        $clazz = $this->optionClazz;
        foreach ($options as $example) {
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
            $repo->saveBatch($this->category, false, ...$toSave);
            $logger->info("register $clazz options from yaml {$this->resource} to option repository.");
        }

    }

    public function register()
    {
    }


}